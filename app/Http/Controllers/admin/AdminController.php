<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\Admin;
use App\Models\AdminsRole;
use Illuminate\Support\Facades\Hash;
use Validator;
use Image;
use Session;
// use Hash;
class AdminController extends Controller
{
    public function dashboard(){
        Session::put('page','dashboard');
        return view('admin.dashboard');
    }

    public function login(Request $request){
        if($request->isMethod('post')){
            $data = $request->all();
            // echo "<pre>"; print_r($data); die;

            $rules = [
                'email' => 'required|email|max:255',
                'password' => 'required|max:30',
            ];

            $customMessages = [
                'email.required' => "Email is required",
                'email.email' => "Valid Email is required",
                'email.required' => "Password is required",
            ];

            $this->validate($request,$rules,$customMessages);

            if(Auth::guard('admin')->attempt(['email' => $data['email'], 'password' => $data['password']])){
                //Remember Admin email and password
                if(isset($data['remember'])&&!empty($data['remember'])){
                    setcookie("email",$data['email'],time()+3600);
                    setcookie("password",$data['password'],time()+3600);
                }else{
                    setcookie("email","");
                    setcookie("password","");
                }

                return redirect('admin/dashboard');
            }else{
                return redirect()->back()->with("error_message", "Invalid Email or Pasword");
            }
        }
        return view('admin.login');
    }

    public function logout(){
        Auth::guard('admin')->logout();
        return redirect('admin/login');
    }

    public function updatePassword(Request $request){
        Session::put('page','update-password');
        if($request->isMethod('post')){
            $data = $request->all();
            // check if current password is current
            if(Hash::check($data['current_password'],Auth::guard('admin')->user()->password)){
                //check if new password and confirm password are matching
                if($data['new_password']==$data['confirm_password']){
                    //update new password
                    Admin::where('id',Auth::guard('admin')->user()->id)->update(['password'=>bcrypt($data['new_password'])]);

                    return redirect()->back()->with('sucess_message','Password has been updated successfully');
                }else{
                    return redirect()->back()->with('error_message','New Password & Confirm password is not matching');
                }
            }else{
                return redirect()->back()->with('error_message','Your current password is incorrectt');
            }
        }
        return view('admin.update_password');
    }
    public function checkCurrentPassword(Request $request){
        $data = $request->all();
        if(Hash::check($data['current_password'],Auth::guard('admin')->user()->password)){
            return "true";
        }else{
            return "false";
        }
    }

    public function updateDetails(Request $request){
        Session::put('page','update-details');
        if($request->isMethod('post')){
            $data = $request->all();
            // echo "<pre>"; print_r($data); die;

            $rules = [
                'admin_name' => 'required|max:255',
                'admin_mobile' => 'required|numeric',
                'admin_img' => 'image',
            ];

            $customMessages = [
                'admin_name.required' => "Name is required",
                'admin_mobile.required' => "Mobile No. is required",
                'admin_mobile.numeric' => "Valid Mobile No. is required",
                'admin_imag.image' => "Valid Image is required",
            ];

            $this->validate($request,$rules,$customMessages);

            //upload admin image
            if($request->hasFile('admin_img')){
                $image_tmp = $request->file('admin_img');
                if($image_tmp->isValid()){
                    $extension = $image_tmp->getClientOriginalExtension();
                    //generate New Image Name
                    $imageName = rand(111,9999).'.'.$extension;
                    $image_path = 'admin/images/photos/'.$imageName;
                    Image::make($image_tmp)->save($image_path);
                }
            }else if(!empty($data['current_image'])){
                $imageName = $data['current_image'];
            }else{
                $imageName = "";
            }

            //Update Admin Details
            Admin::where('email',Auth::guard('admin')->user()->email)->update(['name'=>$data['admin_name'],'mobile'=>$data['admin_mobile'],'image'=>$imageName]);
            return redirect()->back()->with('sucess_message','Admin Details Updated Successfully');
        }
        return view('admin.update_details');
    }

    public function subadmins(){
        Session::put('page','subadmins');
        $subadmins = Admin::where('type', 'subadmin')->get();
        return view('admin.subadmins.subadmins')->with(compact('subadmins'));
    }

      /**
     * Update the specified resource in storage.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateSubadminStatus(Request $request)
    {
        if($request->ajax()){
            $data = $request->all();
            // echo "<pre>"; print_r($data); die;
            if($data['status']=="Active"){
                $status = 0;
            }else{
                $status = 1;
            }
            Admin::where('id',$data['subadmin_id'])->update(['status'=>$status]);
            return response()->json(['status'=>$status,'subadmin_id'=>$data['subadmin_id']]);
        }
    }

   


    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function addEditSubAdmin(Request $request,$id=null)
    {
        Session::put('page','subadmin');
        if($id==""){
            $title = "Add Subadmin";
            $subadmindata = new Admin;
            $message = "Subadmin added successfully";
        }else{
            $title = "Edit Subadmin";
            $subadmindata = Admin::find($id);
            $message = "Subadmin Updated successfully";
        }

        if($request->isMethod('post')){
            $data = $request->all();
            // echo "<pre>"; print_r($data); die;
            if($id==""){
                $subadminCount = Admin::where('email',$data['email'])->count();
                if($subadminCount>0){
                    return redirect()->back()->with('error_message','Subadmin already exsist');
                }
            }
            $rules = [
                
                'name' => 'required',
                'email' => 'required',
                'mobile' => 'required|numeric',
                'password' => 'required'
            ];
            $customMessages = [
                
                'name.required' => 'Name is Required',
                'email.required' => 'Email is Required',
                'mobile.required' => 'Mobile is Required',
                'password.required' => 'Password is Required'
            ];
            $this->validate($request,$rules,$customMessages);
            //upload admin image
            if($request->hasFile('image')){
                $image_tmp = $request->file('image');
                if($image_tmp->isValid()){
                    $extension = $image_tmp->getClientOriginalExtension();
                    //generate New Image Name
                    $imageName = rand(111,9999).'.'.$extension;
                    $image_path = 'admin/images/photos/'.$imageName;
                    Image::make($image_tmp)->save($image_path);
                }
            }else if(!empty($data['current_image'])){
                $imageName = $data['current_image'];
            }else{
                $imageName = "";
            }


            $subadmindata->image = $imageName;
            $subadmindata->name = $data['name'];
            $subadmindata->mobile = $data['mobile'];
            if($id==""){
                $subadmindata->email = $data['email'];
                $subadmindata->type = 'subadmin';
            }
            if($data['password'] !=""){
                $subadmindata->password = bcrypt($data['password']);
            }
           
            $subadmindata->save();
            return redirect('admin/subadmins')->with('success_message', $message);
        }
        return view('admin.subadmins.add_edit_subadmin')->with(compact('title','subadmindata'));

    }

     /**
     * Remove the specified resource from storage.
     * @return \Illuminate\Http\Response
     */
    public function deleteSubadmin($id)
    {
        //Delete Sub Admin
        Admin::where('id',$id)->delete();
        return redirect()->back()->with('success_message', 'Subadmin Delete Sucessfully');
    }

    // public function deleteSubadmin($id){
    //     // Detele Sub Admin
    //     Admin::where('id',$id)->delete();
    //     return redirect()->back()->with('sucess_message', 'Subadmin deleted successfully !');
    // }

    public function updateRole($id,Request $request) {
        $title = "Update Subadmin Roles/Permission";
        if($request->isMethod('post')){
            $data = $request->all();
            // echo "<pre>"; print_r($data); die;

            // Delete all earlier roles
            AdminsRole::where('subadmin_id', $id)->delete();

            // Add New role for subadmin
            if(isset($data['cms_pages']['view'])){
                $cms_pages_view = $data['cms_pages']['view'];
            }else{
                $cms_pages_view = 0;
            }

            if(isset($data['cms_pages']['edit'])){
                $cms_pages_edit = $data['cms_pages']['edit'];
            }else{
                $cms_pages_edit = 0;
            }

            if(isset($data['cms_pages']['full'])){
                $cms_pages_full = $data['cms_pages']['full'];
            }else{
                $cms_pages_full = 0;
            }

            $role = new AdminsRole;
            $role->subadmin_id = $id;
            $role->module = 'cms_pages';
            $role->view_access = $cms_pages_vie;
            $role->edit_access = $cms_pages_edit;
            $role->full_access = $cms_pages_full;
            $role->save();

            $message = "Subadmin Roles Update Sucessfully !";
            return redirect()->back()->with('success_message',$message);
        }

        $subadminRoles = AdminsRole::where('subadmin_id',$id)->get()->toArray();


        return view('admin.subadmins.update_roles')->with(compact('title','id','subadminRoles'));

    }

}
