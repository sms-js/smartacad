<?php

namespace App\Http\Controllers\Admin\Users;

use App\Models\Admin\RolesAndPermissions\Role;
use App\Models\Admin\Users\User;
use App\Models\Admin\Users\UserType;
use Faker\Provider\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Validator;

use App\Http\Requests;
use App\Http\Controllers\Controller;


class UserController extends Controller
{
    /**
     * Redirects To The Inmates Default Page
     * @var string
     */
    protected $redirectTo = '/users';

    /**
     *
     * Make sure the user is logged in
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Get a validator for an incoming registration request.
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $messages = [
            'first_name.required' => 'The First Name is Required!',
            'last_name.required' => 'The Last Name is Required!',
            'user_type_id.required' => 'The User Type is Required!',
            'phone_no.required' => 'The Mobile Number is Required!',
            'gender.required' => 'The Gender is Required!',
            'email.required' => 'A Valid E-Mail Address is Required!',
            'email.unique' => 'This E-Mail Address Has Been Taken or Assigned Already!',
        ];
        return Validator::make($data, [
            'first_name' => 'required|max:100|min:2',
            'last_name' => 'required|max:100|min:2',
            'gender' => 'required',
            'email' => 'required|email|max:255|unique:users,email',
            'phone_no' => 'required',
            'user_type_id' => 'required',
        ], $messages);
    }

    /**
     * Display a listing of the Users.
     * @return Response
     */
    public function getIndex()
    {
        $users = User::all();
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function getCreate()
    {
        $user_types = UserType::orderBy('user_type')->get();
        return view('admin.users.create', compact('user_types'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function postCreate(Request $request)
    {
        $input = $request->all();

        //Validate Request Inputs
        if ($this->validator($input)->fails())
        {
            $this->setFlashMessage('Error!!! You have error(s) while filling the form.', 2);
            return redirect('/users/create')->withErrors($this->validator($input))->withInput();
        }

        //Set the verification code to any random 40 characters and password to random 8 characters
        $verification_code = str_random(40);
        $password = str_random(8);
        $input['verification_code'] = $verification_code;
        $input['password'] = $password;
        $temp = '.';

        // Store the User...
        $user = $this->newUser($input);

        $role = Role::where('user_type_id', $input['user_type_id'])->first();
        $user->attachRole($role);
        ///////////////////////////////////////////////////////// mail sending using $user object ///////////////////////////////////////////
//        if($user){
//            //Assign a role to the user
//            //Verification Mail Sending
//            $content = 'Welcome to printivo, kindly click on the verify link below to complete your registration. Thank You';
//            $content .= "Here are your credentials <br> Username: <strong>" . $user->email . "</strong> <br>";
//            $content .= "Password: <strong>" . $password . "</strong> ";
//            $result = Mail::send('emails.verify', ['user'=>$user, 'content'=>$content], function($message) use($user) {
//                $message->from(env('APP_MAIL'), env('APP_NAME'));
//                $message->subject("Account Verification");
//                $message->to($user->email);
//            });
//            if($result) $temp = ' and a mail has been sent to '.$user->email;
//        }
        // Set the flash message
        $this->setFlashMessage('Saved!!! '.$user->email.' have successfully been saved'.$temp, 1);
        // redirect to the create new warder page
        return redirect('users/create');
    }

    /**
     * Display the password change form
     * @return \Illuminate\View\View
     */
    public function getChange()
    {
        return view('users.change-password');
    }

    /**
     * Change password form via logged in user
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postChange(Request $request)
    {
        $inputs = $request->all();
        $decodeId = $this->getHashIds()->decode($inputs['id']);
        $user = (empty($decodeId)) ? abort(305) : User::findOrFail($decodeId[0]);

        //Keep track of selected tab
        session()->put('active', 'password');

        //Validate if the password match the current password
        if (! Hash::check($inputs['password'], $user->password) ) {
            return redirect('/users/edit/'.$this->getHashIds()->encode($user->user_id ))->withErrors([
                'password' => 'Warning!!! '.$user->first_name.', Your Old Password Credential did not match your current'
            ]);
        }
        if($request->password_confirmation !== $request->new_password){
            return redirect('/users/edit/'.$this->getHashIds()->encode($user->user_id ))->withErrors([
                'password' => 'Warning!!! '.$user->first_name.', Your New and Confirm Password Credential did not match'
            ]);
        }
//         Store the password...
        $user->fill(['password' => Hash::make($request->new_password)])->save();
        // Set the flash message
        $this->setFlashMessage('Changed!!! '.$user->first_name.' Your password change was successful.', 1);
        // redirect to the create a new inmate page
        return redirect('/users/edit/'.$this->getHashIds()->encode($inputs['user_id']));
    }

    /**
     * Activate or Deactivate a User. Activate : 1, Deactivate : 0
     * @param  int  $user_id
     * @param  int  $status
     * @return Response
     */
    public function getStatus($user_id, $status)
    {
        $user = User::findOrFail($user_id);
        if($user !== null) {
            $user->status = $status;
            //Save The Project
            $user->save();
            ($status === '1')
                ? $this->setFlashMessage(' Activated!!! '.$user->fullNames().' have been activated.', 1)
                : $this->setFlashMessage(' Deactivated!!! '.$user->fullNames().' have been deactivated.', 1);
        }else{
            $this->setFlashMessage('Error!!! Unable to perform task try again.', 2);
        }
    }

    /**
     * Displays the user profiles details
     * @param String $encodeId
     * @return \Illuminate\View\View
     */
    public function getView($encodeId)
    {
        $decodeId = $this->getHashIds()->decode($encodeId);
        $user = (empty($decodeId)) ? abort(305) : User::findOrFail($decodeId[0]);
        $type = 'User';
        return view('admin.users.view', compact('user', 'type'));
    }

    /**
     * Displays the user profiles details for editing
     * @param String $encodeId
     * @return \Illuminate\View\View
     */
    public function getEdit($encodeId)
    {
        $decodeId = $this->getHashIds()->decode($encodeId);

        $user = (empty($decodeId)) ? abort(305) : User::findOrFail($decodeId[0]);
        $user_types = UserType::all();

        return view('admin.users.edit', compact('user','user_types'));
    }

    /**
     * Store the form for creating a new resource.
     * @param  Request $request
     * @return Response
     */
    public function postEdit(Request $request)
    {
        //Keep track of selected tab
        session()->put('active', 'info');

        $inputs = $request->all();
        $user = (empty($inputs['user_id'])) ? abort(305) : User::findOrFail($inputs['user_id']);
        $messages = [
            'first_name.required' => 'The First Name is Required!',
            'last_name.required' => 'The Last Name is Required!',
            'gender.required' => 'Gender is Required!',
            'email.required' => 'A Valid E-Mail Address is Required!',
            'email.unique' => 'This E-Mail Address Has Been Taken or Assigned Already!',
            'phone_no.required' => 'The Mobile Number is Required!',
        ];
        $validator = Validator::make($inputs, [
            'first_name' => 'required|max:100|min:2',
            'last_name' => 'required|max:100|min:2',
            'email' => 'required|email|max:255|unique:users,email,'.$user->user_id.',user_id',
            'phone_no' => 'required',
            'gender' => 'required',
        ], $messages);
        //Validate Request Inputs
        if ($validator->fails()) {
            $this->setFlashMessage('  Error!!! You have error(s) while filling the form.', 2);
            return redirect('/users/edit/'.$this->getHashIds()->encode($inputs['user_id']))->withErrors($validator)->withInput();
        }
        // Update the user record
        $user->update($inputs);

        if ($user) {
            // Set the flash message
            $this->setFlashMessage('  Updated!!! ' . $user->fullNames() . ' have successfully been updated.', 1);
            // redirect to the create Committee page and enable the take roll call link
            return redirect('/users');
        }
    }

    /**
     * Create a new user instance after a valid registration.
     * @param  array  $data
     * @return User
     */
    protected function newUser(array $data)
    {
        return User::create([
            'email' => $data['email'],
            'verified' => 1,
//            'password' => Hash::make($data['password']),
            'password' => Hash::make('password'),
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'phone_no' => $data['phone_no'],
            'gender' => $data['gender'],
            'user_type_id' => $data['user_type_id'],
            'verification_code' => $data['verification_code']
        ]);
    }

    /**
     * This will be usedto upload profile image of the user
     * @return mixed
     */
    public function postUploadPicture()
    {
        $inputs = Input::all();
        $file = Input::file('file');

        if (!is_null($file)) {

            $filename = $file->getClientOriginalName();
            $img_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            $user = (empty($inputs['user_id'])) ? abort(305) : User::findOrFail($inputs['user_id']);
            $destinationPath = 'uploads/avartars/';

            $user->avatar = $destinationPath . '' . $user->user_id . '_avatar.' . $img_ext;
            Input::file('file')->move($destinationPath, $user->user_id . '_avatar.' . $img_ext);

            $result = $user->save();
            if ($result) {
                return '<div class="cropping-image-wrap"><img src="/'.$user->avatar.'?'.time().'" class="img-thumbnail" id="crop_image"/></div>';;
            } else {
                return '<div class="alert alert-danger">This format of image is not supported</div>';
            }
        } else {
            return '<div class="alert alert-danger">How did you do that?O_o</div>';
        }
    }

    /**
     *This is used to crop the image before upload is done
     * @return mixed
     */
    public function postCropPicture()
    {
        $inputs = Input::all();

        $imgX = $inputs['ic_x'];
        $imgY = $inputs['ic_y'];
        $imgW = $inputs['ic_w'];
        $imgH = $inputs['ic_h'];

        $user = (empty($inputs['user_id'])) ? abort(305) : User::findOrFail($inputs['user_id']);

        $file = File::get($user->avatar);
        $image = Image::make($file);

//        // crop image
        $image =  $image->crop($imgW, $imgH,$imgX,$imgY);
        $result = $image->save($user->avatar,60);

        if($result){
            $file = File::get($user->avatar);
            Flysystem::connection('awss3')->put($user->avatar,$file);
            return $user->avatar . '?'.time();
//            return $user->avatar;
        }
    }
}
