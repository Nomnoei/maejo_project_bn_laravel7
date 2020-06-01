<?php
namespace App\Http\Controllers\API;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\AddressMember;
use App\AddressOfficer;
use App\TransferHistories;
use App\RateBin;
use App\payments;
use App\notifications;
use App\PaymentPic;
use Illuminate\Support\Facades\Auth;
use Validator;
use File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
// use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
public $successStatus = 200;
/**
     * login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(){

        if(Auth::attempt(['username' => request('username'), 'password' => request('password')])){
            $user = Auth::User();
            // dd($user->id);
            $success['token'] =  $user->createToken('MyApp')-> accessToken;
            $success['user_id'] = $user->id;
            $success['name'] = $user->name;
            $success['tel'] = $user->tel;
            $success['username'] = $user->username;
            $success['status_user'] = $user->status;
            return response()->json(['success' => $success], $this-> successStatus);
        }
        else{
            return response()->json(['error'=>'Unauthorised'], 401);
        }
    }
/**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'tel' => 'required',
            'status' => 'required',
            'username' => 'required',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);
        if ($validator->fails()) {
                    return response()->json(['error'=>$validator->errors()], 401);
                }
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] =  $user->createToken('MyApp')-> accessToken;
        $success['name'] =  $user->name;
        return response()->json(['success'=>$success], $this-> successStatus);
    }

    public function register_member(Request $request)
    {

      $message = [
        'password.min' => 'รหัสผ่านขั้นต่ำต้อง 8 ตัว',
        'tel.unique' =>  'เบอร์มือถือซ้ำกรุณากรอกใหม่'
      ];
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'tel' => 'required|unique:users',
            'password' => 'required|min:8',
        ],$message);
        if ($validator->fails()) {
                    return response()->json(['error'=>$validator->errors()], 401);
                }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create([
         'name' => $request->name,
         'username' => $request->tel,
         'password' =>   $input['password'],
         'tel' => $request->tel,
         'status' => '1',
        ]);

        foreach ($input["address"] as $key => $address) {
            $addressmember = AddressMember::create([
            'user_id' => $user->id,
            'house_num' => $input["address"][$key]["house"],
            'alley' => $input["address"][$key]["Alley"],
            'district' => $input["address"][$key]["district"],
            'moo' =>  $input["address"][$key]["village"],
            'status' => 1,
            'type_pay_id' => 1,
            'patment_date' => date('Y-m-1', strtotime("+1 month"))
            ]);
        }






        // $success['token'] =  $user->createToken('MyApp')-> accessToken;
        // $success['name'] =  $user->name;
        return response()->json(['success'], $this-> successStatus);
    }


/**
     * details api
     *
     * @return \Illuminate\Http\Response
     */
    public function details(Request $request)
    {
      $validator = Validator::make($request->all(), [
          'status' => 'required',
          'username' => 'required'
      ]);
      if ($validator->fails()) {
        return response()->json(['error'=>$validator->errors()], 401);
        }
        $user = User::where('status',$request->status)->where('username',$request->username)->get();
        return response()->json(['success' => $user], $this-> successStatus);
    }
    public function update_member(Request $request){
      $validator = Validator::make($request->all(), [
          'id' => 'required',
          'name' => 'required',
          'tel' => 'required',
          'password' => 'required',
          'status' => 'required'
      ]);
      if ($validator->fails()) {
        return response()->json(['error'=>$validator->errors()], 401);
        }
      $user = User::find($request->id);
      if (Hash::check($request->password, $user->password)) {
        $user = User::where('status',$request->status)->where('id',$request->id)
        ->update([
          'name' => $request->name,
          'tel' => $request->tel
        ]);
          return response()->json(['success' => "ok"], $this-> successStatus);
      }else {

          return response()->json(['password' => ['รหัสผ่านไม่ถูกต้อง']],422);
      }
    }
    public function member()
    {
        $user = User::where('status',1)->get();
        return response()->json(['success' => $user], $this-> successStatus);
    }

    public function this_member(Request $request)
    {
      $validator = Validator::make($request->all(), [
          'user_id' => 'required'
      ]);
      if ($validator->fails()) {
                  return response()->json(['error'=>$validator->errors()], 401);
              }
      // dd($request->all());
        $user = User::where('status',1)->where('id',$request["user_id"])->get();
        return response()->json(['success' => $user], $this-> successStatus);
    }
    public function this_member_address(Request $request)
    {
      $validator = Validator::make($request->all(), [
          'user_id' => 'required',
          'status' => 'required'
      ]);
      if ($validator->fails()) {
                  return response()->json(['error'=>$validator->errors()->all()], 401);
              }
      // dd($request->all());
        $AddressMember = AddressMember::where('status',$request["status"])->where('user_id',$request["user_id"])->get();
        return response()->json(['success' => $AddressMember], $this-> successStatus);
    }
    public function add_one_address(Request $request)
    {
      $validator = Validator::make($request->all(), [
        'user_id' => 'required',
        'house_num' => 'required',
        'alley' =>'required',
        'district' => 'required',
        'moo' =>  'required',
        'status' => 'required',
        'type_pay_id' => 'required'
      ]);
      if ($validator->fails()) {
                  return response()->json(['error'=>$validator->errors()->all()], 401);
              }
      $addressmember = AddressMember::create([
          'user_id' => $request->user_id,
          'house_num' => $request->house_num,
          'alley' => $request->alley,
          'district' => $request->district,
          'moo' =>  $request->moo,
          'status' => $request->status,
          'type_pay_id' => $request->type_pay_id,
          'patment_date' => date('Y-m-1', strtotime("+1 month"))
          ]);

        return response()->json(['success' => $addressmember], $this-> successStatus);
    }
    public function delete_one_address(Request $request)
    {
      $validator = Validator::make($request->all(), [
        'id' => 'required',
      ]);
      if ($validator->fails()) {
                  return response()->json(['error'=>$validator->errors()->all()], 401);
              }
      $address_members = AddressMember::where('id', $request->id)->update(['status' => "2"]);
      // $address_members = AddressMember::where('id',$request->id)->delete();
        return response()->json("Delete Ok", $this-> successStatus);
    }
    public function delete_member(Request $request)
    {
      $validator = Validator::make($request->all(), [
          'id' => 'required',
          'status' => 'required'
      ]);
      if ($validator->fails()) {
                  return response()->json(['error'=>$validator->errors()->all()], 401);
              }
      $user = User::where('status',$request->status)->where('id',$request->id)->update(['status' => "4"]);
      $address_members = AddressMember::where('user_id', $request->id)->update(['status' => "2"]);
      // $address_members = AddressMember::where('id',$request->id)->delete();
        return response()->json("Delete Ok", $this-> successStatus);
    }

    public function Transfer(Request $request)
    {
      $validator = Validator::make($request->all(), [
          'assignee' => 'required',
          'recipient' => 'required',
          'data' => 'required'
      ]);
      if ($validator->fails()) {
                  return response()->json(['error'=>$validator->errors()->all()], 401);
              }
        $input = $request->all();
        $assignee = $request->assignee;
        $recipient = $request->recipient;
        $id_show = rand();

          foreach ($input["data"] as $key => $address) {
        TransferHistories::create([
          'id_show' => $id_show,
          'address_id' => $input["data"][$key]["add_id"],
          'user_id_transfer' =>$assignee,
          'user_id_receive' => $recipient
        ]);
        AddressMember::where('id', $input["data"][$key]["add_id"])->update(['user_id' => $recipient]);
      }
        return response()->json("Ok", $this-> successStatus);
    }

    public function showTransferOfficer(Request $request){
        $validator = Validator::make($request->all(), [
            'status' => 'required'
        ]);
        if ($validator->fails()) {
                    return response()->json(['error'=>$validator->errors()->all()], 401);
                }

        $transfer = DB::table('transfer_histories')
                 ->select('id_show', DB::raw('count(*) as total'))
                 ->groupBy('id_show')
                 ->get();

        $transferHistories = TransferHistories::get();
          $user = User::where('status',$request->status)->get();

          return response()->json(['transfer' => $transfer , 'transferHistories' => $transferHistories ,'user' => $user], $this-> successStatus);
      }

    public function Transfer_Address(Request $request){

        $validator = Validator::make($request->all(), [
            'user_id' => 'required'
        ]);
        if ($validator->fails()) {
                    return response()->json(['error'=>$validator->errors()->all()], 401);
                }

          $transferHistories = TransferHistories::where('id_show' , $request["user_id"])->get();
          $address_members = AddressMember::get();
          return response()->json(['transfer' => $transferHistories , 'address' => $address_members], $this-> successStatus);
      }

    public function officer(Request $request){

        $validator = Validator::make($request->all(), [
            'status' => 'required'
        ]);
        if ($validator->fails()) {
                    return response()->json(['error'=>$validator->errors()->all()], 401);
                }
          $user = User::where('status',$request->status)->get();
          return response()->json(['user' => $user], $this-> successStatus);
      }


    public function RegisterOfficer(Request $request)
        {
          $message = [
            'username.required' => '',
            'username.string' => '',
          ];
          $validator = Validator::make($request->all(), [
              'username' => 'required',
              'name' => 'required',
              'tel' => 'required',
              'password' => 'required',
          ],$message);
          // ]);
            if ($validator->fails())
            {
                      return response()->json(['error'=>$validator->errors()->all()], 401);
            }

          $input = $request->all();
          $input['password'] = bcrypt($input['password']);
          $user = User::create([
           'name' => $request->name,
           'username' => $request->username,
           'password' =>   $input['password'],
           'tel' => $request->tel,
           'status' => '2'
          ]);
          $addressmember = AddressOfficer::create([
          'user_id' => $user->id,
          'house' => $request->house,
          'status' => '1'
          ]);
          return response()->json(['success'], $this-> successStatus);
        }


    public function UpdateMember(Request $request){
          $validator = Validator::make($request->all(), [
              'id' => 'required',
              'name' => 'required',
              'tel' => 'required',
              'status' => 'required'
          ]);
          if ($validator->fails()) {
                      return response()->json(['error'=>$validator->errors()->all()], 401);
                  }

          $user = User::where('status',$request->status)->where('id',$request->id)
          ->update([
            'name' => $request->name,
            'tel' => $request->tel
          ]);
          return response()->json(['success'=>$request->all()], $this-> successStatus);
        }

    public function repassword(Request $request){
      $message = [
        'password.required' => 'ไม่สามารถว่างได้',
        'password.min' => 'รหัสผ่านขั้นต่ำต้อง 8 ตัว',
        'c_password.required' => 'ไม่สามารถว่างได้',
        'c_password.same' => 'password ไม่ตรงกัน'
      ];
          $validator = Validator::make($request->all(), [
              'password' => 'required|min:8',
              'c_password' => 'required|same:password',
          ],$message);
          if ($validator->fails()) {
                      return response()->json(['error'=>$validator->errors()], 401);
                  }
          $input = $request->all();
          $input['password'] = bcrypt($input['password']);
          $user = User::where('id',$input["id"])->update(['password' => $input['password']]);
          return response()->json(['OK'], $this-> successStatus);
        }


    public function repassword_member(Request $request){

            $message = [
              'password.required' => 'ไม่สามารถว่างได้',
              'password.min' => 'รหัสผ่านขั้นต่ำต้อง 8 ตัว',
            ];
          $validator = Validator::make($request->all(), [
              'id' => 'required',
              'status' => 'required',
              'o_password' => 'required',
              'password' => 'required|min:8',
              'c_password' => 'required|same:password',
          ],$message);
          if ($validator->fails()) {
                      return response()->json(['error'=>$validator->errors()], 401);
                  }
          $input = $request->all();
          $input['password'] = bcrypt($input['password']);
          $user = User::find($request->id);
          if (Hash::check($request->o_password, $user->password)) {
                $user = User::where('id',$input["id"])->update(['password' => $input['password']]);
              return response()->json(['success' => "ok"], $this-> successStatus);
          }else {

              return response()->json(['password' => ['รหัสผ่านไม่ถูกต้อง']],422);
          }

        }

    public function ThisOfficer(Request $request){
          $validator = Validator::make($request->all(), [
              'user_id' => 'required',
              'status' => 'required'
          ]);
          if ($validator->fails()) {
                      return response()->json(['error'=>$validator->errors()->all()], 401);
                  }
          $user = User::where('status',$request->status)->where('id',$request["user_id"])->get();
          $AddressOfficer = AddressOfficer::where('user_id',$request["user_id"])->get();
          return response()->json(['success' => $user,'address' => $AddressOfficer], $this-> successStatus);
        }

    public function updateThisOfficer(Request $request){
          $validator = Validator::make($request->all(), [
              'user_id' => 'required',
              'name' => 'required',
              'tel' => 'required',
              'status' => 'required'
          ]);
          if ($validator->fails()) {
                      return response()->json(['error'=>$validator->errors()->all()], 401);
                  }
          $user = User::where('status',$request->status)->where('id',$request->user_id)
          ->update([
            'name' => $request->name,
            'tel' => $request->tel
          ]);
          return response()->json(['success' => $request->all()], $this-> successStatus);
        }

    public function delete_officer(Request $request)
        {
          $validator = Validator::make($request->all(), [
              'id' => 'required',
              'status' => 'required',
              'status_update' => 'required',
              'status_update_address' => 'required'
          ]);
          if ($validator->fails()) {
                      return response()->json(['error'=>$validator->errors()->all()], 401);
                  }
          $user = User::where('status',$request->status)->where('id',$request->id)->update(['status' => $request->status_update]);
          $address_officer = AddressOfficer::where('user_id', $request->id)->update(['status' => $request->status_update_address]);
          // $address_members = AddressMember::where('id',$request->id)->delete();
            return response()->json("Delete Ok", $this-> successStatus);
        }
    public function update_officer_address(Request $request){
      $validator = Validator::make($request->all(), [
          'user_id' => 'required',
          'address' => 'required'
      ]);
      if ($validator->fails()) {
                  return response()->json(['error'=>$validator->errors()->all()], 401);
              }
          $address_officer = AddressOfficer::where('user_id', $request->user_id)->update(['house' => $request->address]);
          return response()->json(["update" => "OK"], $this-> successStatus);
    }


// Payment ---------------------------------------------------------------------------------------------------------------->


    public function rate_price(){
          $RateBin = RateBin::orderBy('created_at', 'DESC')->get();
          return response()->json(['success' => $RateBin], $this-> successStatus);
        }

    public function add_rate_price(Request $request){
          $validator = Validator::make($request->all(), [
              'rate_price' => 'required|numeric',
          ]);
          if ($validator->fails()) {
                      return response()->json(['error'=>$validator->errors()->all()], 401);
                  }
          $RateBin = RateBin::create([
              'rate_price' => $request->rate_price
          ]);
          return response()->json(['success' => $RateBin], $this-> successStatus);
        }

    public function add_Payment(){
              $date_is_pay = date("Y-m-d");
              if (date("d",strtotime($date_is_pay) == "01")) {
              $thai_date_return = "";
              $month = date("m",strtotime($date_is_pay));
                $thai_day_arr=array("อาทิตย์","จันทร์","อังคาร","พุธ","พฤหัสบดี","ศุกร์","เสาร์");
                $thai_month_arr=array(
                 "00"=>"",
                 "01"=>"มกราคม",
                 "02"=>"กุมภาพันธ์",
                 "03"=>"มีนาคม",
                 "04"=>"เมษายน",
                 "05"=>"พฤษภาคม",
                 "06"=>"มิถุนายน",
                 "07"=>"กรกฎาคม",
                 "08"=>"สิงหาคม",
                 "09"=>"กันยายน",
                 "10"=>"ตุลาคม",
                 "11"=>"พฤศจิกายน",
                 "12"=>"ธันวาคม"
                );
              $thai_date_return = $thai_month_arr[$month];
              $users = DB::table('users')->where('status',1)->orderByRaw('id DESC')->get();
              $AddressMembers = DB::table('address_members')->orderByRaw('id DESC')->where('status',1)->get();
              $rate_max = DB::table('rate_bins')->max('created_at');
              $rate_binss = DB::table('rate_bins')->where('created_at', $rate_max)->get();
              $payments =  DB::table('payments')->where('status', 1)->orderByRaw('payment_id DESC')->get();
              foreach ($payments as $j => $payment) {
                if($payment->status = 1){
                  foreach ($AddressMembers as $AddressMember) {
                      if ($payment->address_id == $AddressMember->id) {
                        $user_id =  $AddressMember->user_id;
                        $address_id = $AddressMember->id;
                        break;
                      }
                      break;
                }
              }
              foreach ($AddressMembers as $AddressMember) {
                if ($AddressMember->patment_date < $date_is_pay) {
                  DB::table('payments')
                      ->where('payment_id',$payment->payment_id)
                      ->where('address_id',$AddressMember->id)
                      ->where('status', 1)
                      ->update(['status' => 5]);
                  }
                else {
                }
              }
              }
              foreach ($users as $user) {
                $AddressMembers = DB::table('address_members')->where('user_id',$user->id)->where('patment_date','<=',$date_is_pay)->orderByRaw('id DESC')->where('status',1)->get(); //update_date
              $payment_id ="";
              foreach ($rate_binss as $rate_bins) {
                  $rate_price = $rate_bins->rate_price;
              }
              foreach ($AddressMembers as $key => $AddressMember) {
                  $address_ids[$key] = $AddressMember->id;
                  $price[$key] = $rate_price * $AddressMember->type_pay_id;
              }
                  $price_old = 0;
                  $date = date("Y-m-d");
                  $status = 1;
                  $payments_max = DB::table('payments')->max('payment_id');

                    if(empty($payments_max)){
                          $payment_id = 1;
                    }else {

                        $payment_id = $payments_max+1;


                    }
                    foreach ($AddressMembers as $i => $AddressMember) {
                      $payments2 = payments::create([
                        'payment_id' => $payment_id,
                        'address_id' => $address_ids[$i],
                        'price' => $price[$i],
                        'price_old' => $price_old,
                        'date' => $date,
                        'status' => $status,
                        ]);
                        $notifications = notifications::create([
                              'whatever_id' => '000',
                              'user_id' => $user->id,
                              'type' => 1,
                              'detail'=> "ถึงกำหนดจ่ายเงินเดือน $thai_date_return แล้ว ของบ้านเลขที่ $AddressMember->house_num",
                              'status' => 1

                          ]);
                          $month_type = $AddressMember->type_pay_id;
                          DB::table('address_members')
                            ->where('id',$AddressMember->id)
                            ->update(['patment_date' => date('Y-m-1', strtotime("+ ".$AddressMember->type_pay_id." month"))]);
                      }
                      foreach ($payments as $j => $payment) {
                         //dd($payments);

                            $payments_id_old[$j] = $payment->payment_id;
                            $house_old[$j] = $payment->address_id;
                            $price_old2[$j] = $payment->price + $payment->price_old;
                            DB::table('payments')
                              ->where('payment_id', $payment_id)
                              ->where('address_id', $house_old[$j])
                              ->update(['price_old' => $price_old2[$j]]);

                      }
                    }
                  }
                      return response()->json(['success'], $this-> successStatus);
                }

    public function select_pay(Request $request){

          $validator = Validator::make($request->all(), [
              'id' => 'required',
              'status_user' => 'required',
              'status_address' => 'required',
              'status_payments' => 'required'
          ]);
          if ($validator->fails()) {
                      return response()->json(['error'=>$validator->errors()->all()], 401);
                  }
              // $input = $request->all();
              $user = User::where('status',$request->status_user)->where('id',$request->id)->get();
              $address_members = AddressMember::where('status',$request->status_address)->where('user_id',$request->id)->get();
              $payments = payments::where('status',$request->status_payments)->get();
              return response()->json(['user' => $user , 'address' => $address_members , 'payments' => $payments], $this->successStatus);
        }

    public function TransferAgain(Request $request){
      $validator = Validator::make($request->all(), [
          'id' => 'required',
          'status_pay_pic' => 'required'
      ]);
      if ($validator->fails()) {
                  return response()->json(['error'=>$validator->errors()->all()], 401);
              }
              $PaymentPic = PaymentPic::where('id',$request->id)->where('status',$request->status_pay_pic)->get();
              $payments_g = DB::table('payment_pics')
                             ->select(DB::raw('sum(price) as sum ,count(*) as total,id'))
                             ->where('status',$request->status_pay_pic)
                             ->where('id',$request->id)
                             ->groupBy('id')
                             ->get();
          return response()->json(['PaymentPic' => $PaymentPic , 'paymentsgroupBy' => $payments_g], $this->successStatus);
    }

    public function select_pay_all(Request $request){

          $validator = Validator::make($request->all(), [
              'status_user' => 'required',
              'status_address' => 'required',
              'status_payments' => 'required'
          ]);
          if ($validator->fails()) {
                      return response()->json(['error'=>$validator->errors()->all()], 401);
                  }
              // $input = $request->all();
              $user = User::where('status',$request->status_user)->get();
              $address_members = AddressMember::where('status',$request->status_address)->get();
              $payments = payments::where('status',$request->status_payments)->get();
              $payment_pics = PaymentPic::where('status',$request->status_address)->get();

              $payments_g = DB::table('payment_pics')
                             ->select(DB::raw('sum(price) as total ,id '))
                             ->groupBy('id')
                             ->get();
              return response()->json(['user' => $user , 'address' => $address_members , 'payments' => $payments, 'PaymentPic' => $payment_pics,'payments_g' => $payments_g], $this->successStatus);
        }

    public function image_slip(Request $request){
            $input = $request->all();
            session_start();
            $PaymentPics_max = DB::table('payment_pics')->max('id');
              if(empty($PaymentPics_max)){
                    $payment_pic_id = 1;
              }else {
                  $payment_pic_id = $PaymentPics_max+1;
              }
            move_uploaded_file($_FILES["file"]["tmp_name"], "slip/".$payment_pic_id."_".date("Y-m-d").$_FILES['file']['name']);
            $_SESSION["file_name"] = $payment_pic_id."_".date("Y-m-d").$_FILES['file']['name'];

                $_SESSION["payment_pic_id"] = $payment_pic_id;
            return response()->json(['success'], $this-> successStatus);
          }

    public function upload_slip(Request $request){
            session_start();
            $input = $request->all();
            $validator = Validator::make($request->all(), [
                'data' => 'required'
            ]);
            if ($validator->fails()) {
                        return response()->json(['error'=>$validator->errors()->all()], 401);
                    }

                    foreach ($input["data"] as $key => $value) {
                      $PaymentPic = PaymentPic::create([
                      'id' => $_SESSION["payment_pic_id"],
                      'address_id' => $input["data"][$key]["address_id"],
                      'payment_id' => $input["data"][$key]["payment_id"],
                      'picture' => $_SESSION["file_name"],
                      'price'  => $input["data"][$key]["price"],
                      'status' => $input["data"][$key]["status"],
                      ]);

                      payments::where('status', 1)->where('address_id',$input["data"][$key]["address_id"])->
                      update([
                        'status' => 3
                      ]);


                    }


            return response()->json(['success'], $this-> successStatus);
          }

    public function UpdateSlip(Request $request){
      session_start();
      $input = $request->all();
      $validator = Validator::make($request->all(), [
          'data' => 'required',
          'id' => 'required',
          'user_id' => 'required',
          'notifications' => 'required'
      ]);
      if ($validator->fails()) {
          return response()->json(['error'=>$validator->errors()->all()], 401);
        }

        foreach ($input["data"] as $key => $value) {

          $PaymentPic = PaymentPic::where('id',$request->id)->update([
          'picture' => $_SESSION["file_name"],
          'status' => 1,
          ]);




          payments::where('status', 6)->where('address_id',$input["data"][$key]["address_id"])->
          update([
            'status' => 3
          ]);


        }
        notifications::where('id',$request->notifications)->delete();
        return response()->json(['success'], $this-> successStatus);
    }

    public function payment_confirm(Request $request){
            $validator = Validator::make($request->all(), [
                'data' => 'required'
            ]);
            if ($validator->fails()) {
              return response()->json(['error'=>$validator->errors()->all()], 401);
            }

              $input = $request->all();
            foreach ($input["data"] as $key => $value) {
              $PaymentPic = PaymentPic::where('id',$input["data"][$key]['id'])->update([
                'status' => "2"
              ]);
            }

            foreach ($input["data"] as $key1 => $value) {
              foreach ($input["data"][$key1]["detiil"] as $key2 => $value) {
                foreach ($input["data"][$key1]["detiil"][$key2] as $key3 => $value) {
                  $Payment = payments::where('payment_id',$input["data"][$key1]['detiil'][$key2]['Payment_id'])
                  ->where('address_id',$input["data"][$key1]['detiil'][$key2]['address_id'])
                  ->update([
                   'status' => "4"
                 ]);
                }
              }
              $notifications = notifications::create([
              'whatever_id' => '0',
              'user_id' => $input["data"][$key1]["user_id"],
              'type' => 3,
              'detail'=> "หลักฐานการจ่ายค่าเก็บขยะได้รับการยืนยันแล้ว",
              'status' => 1
              ]);
            }



              return response()->json(['success'=> $input["data"]], $this-> successStatus);
           }


    public function cancel_payment(Request $request){
      $validator = Validator::make($request->all(), [
          'payment_id' => 'required',
          'user_id' => 'required'
      ]);
      if ($validator->fails()) {
        return response()->json(['error'=>$validator->errors()->all()], 401);
      }
      $payment_pics =  DB::table('payment_pics')->where('id', $request->payment_id)->orderByRaw('payment_id DESC')->get();
      $AddressMembers = DB::table('address_members')->orderByRaw('id DESC')->where('status',1)->get();
        foreach ($payment_pics as $payment_pic) {
          DB::table('payments')
              ->where('address_id', $payment_pic->address_id)
              ->where('payment_id',$payment_pic->payment_id)
              ->update(['status' => 6]);

              $name_pic = "slip/".$payment_pic->picture;
        }

        DB::table('payment_pics')
          ->where('id', $request->payment_id)
          ->update(['status' => 3]);

      // Storage::disk('public')->delete($name_pic);
      if(File::exists($name_pic)) {
            File::delete($name_pic);
        }

      $notifications = notifications::create([
          'whatever_id' => $request->payment_id,
          'user_id' => $request->user_id,
          'type' => 4,
          'detail'=> "หลักฐานการชำระเงินของคุณไม่ถูกต้อง กรุณาส่งหลักฐานใหม่",
          'status' => 1

      ]);


        return response()->json(['success' => $request->all()], $this-> successStatus);
    }


    public function payment_history_admin(){
            // $payments_pay = payments::whete('status',4)->get();
            // $payments_notpay = payments::whete('status',1)->whete('status',5)->get();
            // $payments_num = DB::table('payments')->orderByRaw('payment_id DESC')
            //   ->select(DB::raw('count(payment_id) as `data`'),DB::raw('YEAR(date) year, MONTH(date) month'))
            //   ->where('status', 3)
            //   ->groupby('year','month')
            //   ->get();
            // $payments_pay =  DB::table('payments')->where('status', 2)->orderByRaw('payment_id DESC')->get();
            $payments = DB::table('payments')->get();
            $all_date_pay = DB::table('payments')->select('date')->orderByRaw('payment_id DESC')->groupby('date')->get();
            $payments_paid = DB::table('payments')->orderByRaw('payment_id DESC')
            ->select(DB::raw('count(status) as num_pay,date'))
            ->where('status',2)
            ->groupby('date')
            ->get();
            $payments_not = DB::table('payments')->orderByRaw('payment_id DESC')
            ->select(DB::raw('count(status) as num_pay,date'))
            ->where('status',1)
            ->orWhere('status',3)
            ->orWhere('status',5)
            ->groupby('date')
            ->get();
             return response()->json(['num' => $all_date_pay,'payments_paid' => $payments_paid , 'payments_not' => $payments_not , 'payments' => $payments], $this-> successStatus);
            }

    public function history_paid(Request $request){
              $validator = Validator::make($request->all(), [
                  'date' => 'required',
                  'status' => 'required',
                  'Orstatus' => 'required'
              ]);
              if ($validator->fails()) {
                          return response()->json(['error'=>$validator->errors()->all()], 401);
                      }
                      $payments_g = DB::table('payments')->select('payment_id')->orderByRaw('payment_id DESC')->where('status',$request->status)->orWhere('status',$request->Orstatus)->where('date',$request->date)->groupby('payment_id')->get();
                      $payments = payments::where('date',$request->date)->where('status',$request->status)->orWhere('status',$request->Orstatus)->get();
                      $User = User::where('status',1)->get();
                      $AddressMember = AddressMember::where('status',1)->get();

              return response()->json(['AddressMember' => $AddressMember,'payments' => $payments,'User'=>$User ,'payments_g' => $payments_g], $this-> successStatus);
                }

    public function NotiFication(Request $request){
      $validator = Validator::make($request->all(), [
          'user_id' => 'required',
      ]);
      if ($validator->fails()) {
                  return response()->json(['error'=>$validator->errors()->all()], 401);
              }

        $notifications = notifications::where('user_id' , $request->user_id)->orderBy('id', 'DESC')->get();
        $notifications_num = notifications::where('user_id' , $request->user_id)->where('status',"1")->orderBy('id', 'DESC')->get();

        return response()->json(['notifications' => $notifications , 'notifications_num' => $notifications_num], $this-> successStatus);
    }


    public function bill(Request $request){
      $validator = Validator::make($request->all(), [
          'payment_id' => 'required',
      ]);
      if ($validator->fails()) {
          return response()->json(['error'=>$validator->errors()->all()], 401);
      }
        $payments = payments::where('status',"1")->orWhere('status',"3")->orWhere('status',5)->get();
        $AddressMembers = AddressMember::get();
        $RateBins = RateBin::get();
        $user = User::where('status',1)->get();
        return response()->json(['payments' => $payments , 'AddressMember' => $AddressMembers , 'RateBin' => $RateBins, 'id' => $request->all(),'user' => $user], $this-> successStatus);
    }
    public function not_pay_data(Request $request){
      $validator = Validator::make($request->all(), [
          'status' => 'required',
          'Orstatus' => 'required',
      ]);
      if ($validator->fails()) {
          return response()->json(['error'=>$validator->errors()->all()], 401);
      }
      $payments = payments::where('status',$request->status)->orWhere('status',$request->Orstatus)->orWhere('status',5)->get();
      $payments_not = DB::table('payments')->orderByRaw('payment_id DESC')
      ->select(DB::raw('count(status) as num_pay,payment_id'))
      ->where('status',$request->status)
      ->orWhere('status',$request->Orstatus)
      ->orWhere('status',5)
      ->groupby('payment_id')
      ->get();

        $user = User::where('status',1)->get();
        $AddressMembers = AddressMember::get();
        return response()->json(['payments' => $payments,'payments_g' => $payments_not,'user' => $user , 'AddressMember' => $AddressMembers], $this-> successStatus);
    }

    public function PrintReceipt(Request $request){
      $validator = Validator::make($request->all(), [
          'id' => 'required',
          'date' => 'required',
          'status' => 'required',
          'status_user' => 'required',

      ]);
      if ($validator->fails()) {
          return response()->json(['error'=>$validator->errors()->all()], 401);
      }
      $payments = payments::where('status',$request->status)->where('payment_id',$request->id)->where('date',$request->date)->get();
      $AddressMembers = AddressMember::get();
      $user = User::where('status',$request->status_user)->get();
      $payments_g = payments::where('status',$request->status)
                     ->select(DB::raw('sum(price) as sum ,payment_id'))
                     ->where('payment_id',$request->id)
                     ->groupBy('payment_id')
                     ->get();
        return response()->json(['payments' => $payments , 'AddressMembers' => $AddressMembers , 'user' => $user , 'payments_g' => $payments_g], $this-> successStatus);
    }


    public function notifications_reset(Request $request){
      $validator = Validator::make($request->all(), [
          'id' => 'required',
      ]);
      if ($validator->fails()) {
          return response()->json(['error'=>$validator->errors()->all()], 401);
      }
      $notifications = notifications::where('user_id' , $request->id)->update([
        'status' => "2"
      ]);
      return response()->json(['user' => $request->all()], $this-> successStatus);
    }

    public function history(Request $request){
      $validator = Validator::make($request->all(), [
          'user_id' => 'required',
      ]);
      if ($validator->fails()) {
          return response()->json(['error'=>$validator->errors()->all()], 401);
      }
          $payments = payments::where('status',"4")->get();
          $address_members = AddressMember::where('user_id', $request->user_id)->get();
          $user = User::where('id' , $request->user_id)->get();
          $payments_num = DB::table('payments')->orderByRaw('payment_id DESC')
          ->select(DB::raw('count(date) as `date`'),DB::raw('YEAR(date) year, MONTH(date) month'))->where('status', 4)
          ->groupby('year','month')
          ->get();
          $payments_g = DB::table('payments')->orderByRaw('payment_id DESC')
          ->select(DB::raw('count(status) as num_pay,date'))
          ->where('status',4)
          ->groupby('date')
          ->get();
        return response()->json(['payment' => $payments , 'address' => $address_members , 'user' => $user , 'payments_num' => $payments_g], $this-> successStatus);
    }

    public function update_pay_type(Request $request){
      $validator = Validator::make($request->all(), [
          'user_id' => 'required',
          'status' => 'required',
          'items' => 'required'
      ]);
      if ($validator->fails()) {
          return response()->json(['error'=>$validator->errors()->all()], 401);
      }
      $input = $request->all();
      foreach ($input["items"] as $key => $value) {
          $address_members = AddressMember::where('id', $input["items"][$key]["id"])->update(['type_pay_id' => $input["items"][$key]["selected"]]);
      }


      return response()->json(['success' => $input["items"][0]["selected"]], $this-> successStatus);
    }




    public function test(Request $request){
            $input = $request->all();
            return response()->json(['success' => $request->all()], $this-> successStatus);
              }


}
