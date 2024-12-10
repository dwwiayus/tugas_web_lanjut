<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use App\Mail\Websitemail;
use Illuminate\Support\Facades\Mail;

class AdminController extends Controller
{
    public function AdminLogin()
    {
        return view('admin.login');
    }

    public function AdminDashboard()
    {
        return view('admin.index');
    }

    public function AdminLoginSubmit(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $check = $request->all();
        $data = [
            'email' => $check['email'],
            'password' => $check['password'],
        ];

        if (Auth::guard('admin')->attempt($data)) {
            return redirect()->route('admin.dashboard')->with('success', 'Login successful');
        } else {
            return redirect()->route('admin.login')->with('error', 'Invalid Credentials');
        }
    }
    public function AdminLogout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login')->with('success', 'Logout successful');
    }
    public function AdminForgetPassword()
    {
        return view('admin.forget_password');
    }
    public function AdminSubmitPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);
        $admin_data = Admin::where('email', $request->email)->first();
        if (!$admin_data) {
            return redirect()->back()->with('error', 'Email not found');
        }
        $token = hash('sha256', time());
        $admin_data->token = $token;
        $admin_data->update();

        $reset_link = url('admin/reset_password/' . $token . '/' . $request->email);
        $subject = "Reset Password";
        $message = "Please click on below link to reset password <br>";
        $message .= "<a href='" . $reset_link . "'>click here</a>";
        Mail::to($request->email)->send(new Websitemail($subject, $message));
        return redirect()->back()->with('success', 'Reset Password Link has been sent to your email');
    }
    public function AdminResetPassword($token, $email)
    {
        $admin_data = Admin::where('email', $email)->where('token', $token)->first();
        if (!$admin_data) {
            return redirect()->route('admin.login')->with('error', 'Token expired or invalid');
        }
        return view('admin.reset_password', compact('token', 'email'));
    }
    public function AdminResetSubmitPassword(Request $request)
    {
        $request->validate([
            'password' => 'required',
            'password_confirmation' => 'required|same:password'
        ]);
        $admin_data = Admin::where('email', $request->email)->where('token', $request->token)->first();
        $admin_data->password =  Hash::make($request->password);
        $admin_data->token = "";
        $admin_data->update();
        return redirect()->route('admin.login')->with('success', 'Password reset successful. Please login now');

    }

    public function AdminProfile(){
        $id=Auth::guard('admin')->id();
        $profileData = Admin::find($id);
        return view('admin.admin_profile',compact('profileData'));
    }

    public function AdminProfileStore(Request $request){
        $id = Auth::guard('admin')->id();
        $data = Admin::find($id);

        $data->name = $request->name;
        $data->email = $request->email;
        $data->phone = $request->phone;
        $data->address = $request->address; 

        $oldPhotoPath = $data->photo;

        if ($request->hasFile('photo')) {
           $file = $request->file('photo');
           $filename = time().'.'.$file->getClientOriginalExtension();
           $file->move(public_path('upload/admin_images'),$filename);
           $data->photo = $filename;

           if ($oldPhotoPath && $oldPhotoPath !== $filename) {
             $this->deleteOldImage($oldPhotoPath);
           }

        }
        $data->save();  
        $notification = array(
            'message' => 'Profile Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    }
     // End Method 
     private function deleteOldImage(string $oldPhotoPath): void {
        $fullPath = public_path('upload/admin_images/'.$oldPhotoPath);
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
     }
     // End Private Method 
    }