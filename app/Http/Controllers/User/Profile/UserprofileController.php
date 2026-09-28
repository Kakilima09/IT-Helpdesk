<?php

namespace App\Http\Controllers\User\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Customer;
use App\Models\CustomerSetting;
use App\Models\Countries;
use App\Models\Timezone;
use Auth;
use Hash;
use File;
use App\Models\Apptitle;
use App\Models\Footertext;
use App\Models\Seosetting;
use App\Models\Pages;
use Illuminate\Support\Facades\Validator;
use Image;
use App\Models\TicketCustomfield;
use Illuminate\Validation\Rule;

class UserprofileController extends Controller
{
    /**
     * Menampilkan halaman profile user
     */
    public function profile()
    {
        if (!Auth::guard('customer')->check()) {
            return redirect()->route('customer.login')->with('error', 'Please login first.');
        }

        $user = Auth::guard('customer')->user();

        // Pastikan hanya user dengan role 'user'
        if ($user->role !== 'user') {
            return redirect()->back()->with('error', 'Access denied.');
        }

        // Ambil data pendukung
        $countries = Countries::all();
        $timezones = Timezone::orderBy('group')->get();

        // Data umum (title, footer, seo, dll.)
        $title = Apptitle::first();
        $footertext = Footertext::first();
        $seopage = Seosetting::first();
        $post = Pages::all();

        $data = compact('user', 'countries', 'timezones', 'title', 'footertext', 'seopage', 'post');

        // Gunakan view user.profile.edit
        return view('user.profile.edit', $data);
    }

    /**
     * Update profile user (lengkap dengan password, foto, dll)
     */
    public function profilesetup(Request $request)
    {
        if (!Auth::guard('customer')->check()) {
            return redirect()->route('customer.login')->with('error', 'Please login first.');
        }

        // $user = Auth::guard('customer')->user();
        // if ($user->role !== 'user') {
        //     return redirect()->back()->with('error', 'Access denied.');
        // }

        // Validasi dasar
        $rules = [
            'firstname' => 'required|string|max:255',
            'lastname'  => 'nullable|string|max:255',
            'email'     => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone'     => 'nullable|string|max:20',
            'country'   => 'nullable|string|max:100',
            'timezone'  => 'nullable|string|max:100',
            'languages' => 'nullable|array',
            'skills'    => 'nullable|array',
            'notification_pref' => 'nullable|in:email,whatsapp,both',
            'image'     => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5120', // 5MB
        ];

        // Jika password diisi, tambahkan validasi
        if ($request->filled('password')) {
            $rules['current_password'] = 'required|string';
            $rules['password'] = 'required|string|min:8|confirmed';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Cek current password jika password diisi
        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return redirect()->back()->withErrors(['current_password' => 'Password saat ini salah.'])->withInput();
            }
        }

        // Update data dasar
        $user->firstname = $request->input('firstname');
        $user->lastname  = $request->input('lastname');
        $user->email     = $request->input('email');
        $user->phone     = $request->input('phone');
        $user->country   = $request->input('country');
        $user->timezone  = $request->input('timezone');
        $user->languagues = $request->filled('languages') ? implode(',', $request->languages) : null;
        $user->skills    = $request->filled('skills') ? implode(',', $request->skills) : null;
        $user->notification_pref = $request->input('notification_pref', 'email');

        // Update password jika diisi
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        // Upload foto
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $validator = Validator::make(['image' => $file], [
                'image' => 'mimes:jpeg,jpg,png|required|max:5120'
            ]);

            if ($validator->fails()) {
                return redirect()->back()->with('error', 'Please check the format and size of the file.');
            }

            $destination = public_path('/uploads/profile');
            if (!File::exists($destination)) {
                File::makeDirectory($destination, 0755, true);
            }

            $image_name = time() . '.' . $file->getClientOriginalExtension();
            $resize_image = Image::make($file->getRealPath());
            $resize_image->resize(80, 80, function ($constraint) {
                $constraint->aspectRatio();
            })->save($destination . '/' . $image_name);

            // Hapus foto lama
            if ($user->image) {
                $old_image_path = public_path('/uploads/profile/' . $user->image);
                if (File::exists($old_image_path)) {
                    File::delete($old_image_path);
                }
            }
            $user->image = $image_name;
        }

        $user->save();

        return redirect()->route('user.dashboard')->with('success', 'Your profile has been successfully updated.');
    }

    /**
     * Hapus foto profil
     */
    public function imageremove(Request $request, $id)
    {
        if (!Auth::guard('customer')->check()) {
            return response()->json(['error' => 'Please login first.'], 401);
        }

        // $user = Auth::guard('customer')->user();
        // if ($user->id != $id || $user->role !== 'user') {
        //     return response()->json(['error' => 'Access denied.'], 403);
        // }

        if ($user->image) {
            $image_path = public_path('/uploads/profile/' . $user->image);
            if (File::exists($image_path)) {
                File::delete($image_path);
            }
            $user->image = null;
            $user->save();
        }

        return response()->json(['success' => 'The profile image was successfully removed.']);
    }

    /**
     * Hapus akun user
     */
    public function profiledelete($id)
    {
        if (!Auth::guard('customer')->check()) {
            return response()->json(['error' => 'Please login first.'], 401);
        }

        // $user = Auth::guard('customer')->user();
        // if ($user->id != $id || $user->role !== 'user') {
        //     return response()->json(['error' => 'Access denied.'], 403);
        // }

        Auth::guard('customer')->logout();

        $tickets = $user->tickets()->get();
        foreach ($tickets as $ticket) {
            foreach ($ticket->getMedia('ticket') as $media) {
                $media->delete();
            }
            foreach ($ticket->comments()->get() as $comment) {
                foreach ($comment->getMedia('comments') as $media) {
                    $media->delete();
                }
                $comment->delete();
            }
            $ticket->delete();
        }

        if ($user->custsetting) {
            $user->custsetting()->delete();
        }

        TicketCustomfield::where('cust_id', $user->id)->delete();
        $user->delete();

        return response()->json(['success' => 'Your account has been deleted!']);
    }

    /**
     * Update setting dark mode
     */
    public function custsetting(Request $request)
    {
        if (!Auth::guard('customer')->check()) {
            return response()->json(['error' => 'Please login first.'], 401);
        }

        // $user = Auth::guard('customer')->user();
        // if ($user->role !== 'user') {
        //     return response()->json(['error' => 'Access denied.'], 403);
        // }

        if ($user->id != $request->cust_id) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        if ($user->custsetting != null) {
            $user->custsetting->darkmode = $request->dark;
            $user->custsetting->save();
        } else {
            CustomerSetting::create([
                'custs_id' => $user->id,
                'darkmode' => $request->dark
            ]);
        }

        return response()->json(['code' => 200, 'success' => 'Updated Successfully'], 200);
    }
}