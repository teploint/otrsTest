<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use DB;
class Reminder extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {

        $this->middleware('rights',  ['except' => ['reminderbox']]);
    }

    public function index()
    {
       //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->get('data');
        $hash = json_decode($data);
        $principal = explode("@",$_SERVER['REMOTE_USER']);
        $user_login = $principal[0];

        if ($hash->id_type == 2) { /** Еженедельное **/

            DB::table('reminders')->insert(
                array(
                    'id' => null,
                    'name' => trim($hash->name),
                    'id_type' => $hash->id_type,
                    'weekday' => $hash->weekday,
                    'hour' => $hash->hour,
                    'minute' => $hash->minute,
                    'subject' => trim($hash->subject),
                    'a_body' => trim($hash->a_body),
                    'status' => $hash->status,
                    'deleted' => 0,
                    'updated_at' => null,
                    'created_at' => null,
                    'updated_user' => $user_login,
                    'created_user' => $user_login)
            );

        } else if ($hash->id_type == 3) { /** Ежемесячное **/

            DB::table('reminders')->insert(
                array(
                    'id' => null,
                    'name' => trim($hash->name),
                    'id_type' => $hash->id_type,
                    'day' => $hash->day,
                    'hour' => $hash->hour,
                    'minute' => $hash->minute,
                    'subject' => trim($hash->subject),
                    'a_body' => trim($hash->a_body),
                    'status' => $hash->status,
                    'deleted' => 0,
                    'updated_at' => null,
                    'created_at' => null,
                    'updated_user' => $user_login,
                    'created_user' => $user_login)
            );

        } else if ($hash->id_type == 4) { /** Точная дата **/

            $date = $hash->exactdate;
            $day= date("d", strtotime($date));
            $month = date("m", strtotime($date));
            $year = date("Y", strtotime($date));

            DB::table('reminders')->insert(
                array(
                    'id' => null,
                    'name' => trim($hash->name),
                    'id_type' => $hash->id_type,
                    'year' => $year,
                    'month' => $month,
                    'day' => $day,
                    'hour' => $hash->hour,
                    'minute' => $hash->minute,
                    'subject' => trim($hash->subject),
                    'a_body' => trim($hash->a_body),
                    'status' => $hash->status,
                    'deleted' => 0,
                    'updated_at' => null,
                    'created_at' => null,
                    'updated_user' => $user_login,
                    'created_user' => $user_login)
            );

        }


        return json_encode(array(
            "success" => true,
            "data" => $user_login
        ));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->get('data');
        $hash = json_decode($data);
        $principal = explode("@",$_SERVER['REMOTE_USER']);
        $user_login = $principal[0];

        if ($hash->id_type == 2) { /** Еженедельное **/

            DB::update('update reminders set name = ?, id_type = ?, weekday = ?, hour = ?, minute = ?, subject = ?, a_body = ?, status = ?, updated_user = ? where id = ?', array(

                    $name = trim($hash->name),
                    $id_type = trim($hash->id_type),
                    $weekday = trim($hash->weekday),
                    $hour = trim($hash->hour),
                    $minute = trim($hash->minute),
                    $subject = trim($hash->subject),
                    $a_body = trim($hash->a_body),
                    $status = trim($hash->status),
                    $updated_user = $user_login,
                    (int) $id = $id)
            );

        } else if ($hash->id_type == 3) { /** Ежемесячное **/

            DB::update('update reminders set name = ?, id_type = ?, day = ?, hour = ?, minute = ?, subject = ?, a_body = ?, status = ?, updated_user = ? where id = ?', array(

                    $name = trim($hash->name),
                    $id_type = trim($hash->id_type),
                    $day = trim($hash->day),
                    $hour = trim($hash->hour),
                    $minute = trim($hash->minute),
                    $subject = trim($hash->subject),
                    $a_body = trim($hash->a_body),
                    $status = trim($hash->status),
                    $updated_user = $user_login,
                    (int) $id = $id)
            );

        } else if ($hash->id_type == 4) { /** Точная дата **/

            $date = $hash->exactdate;
            $day= date("d", strtotime($date));
            $month = date("m", strtotime($date));
            $year = date("Y", strtotime($date));

            DB::update('update reminders set name = ?, id_type = ?, year = ?, month = ?,  day = ?, hour = ?, minute = ?, subject = ?, a_body = ?, status = ?, updated_user = ? where id = ?', array(

                    $name = trim($hash->name),
                    $id_type = trim($hash->id_type),
                    $year = $year,
                    $month = $month,
                    $day = $day,
                    $hour = trim($hash->hour),
                    $minute = trim($hash->minute),
                    $subject = trim($hash->subject),
                    $a_body = trim($hash->a_body),
                    $status = trim($hash->status),
                    $updated_user = $user_login,
                    (int) $id = $id)
            );

        }


        return json_encode(array(
            "success" => true,
            "data" => $user_login
        ));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function reminderbox()
    {
        $results = DB::select('select * from reminders_type order by reminders_type.name');
        return json_encode(array(
            "success" => true,
            "data" => $results
        ));
    }

    public function reminderdell(Request $request)
    {
        $data = $request->get('data');
        $principal = explode("@",$_SERVER['REMOTE_USER']);
        $user_login = $principal[0];
        DB::update('update reminders set deleted = ?, updated_user = ?  where id = ?', array(
                $deleted = 1,
                $updated_user = $user_login,
                (int) $data)
        );
        return json_encode(array(
            "success" => true,
            "data" => $user_login
        ));
    }
}
