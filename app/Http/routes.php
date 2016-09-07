<?php


/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

/*Route::get('/', function () {
    return view('welcome');
});*/


Route::get('/123', function () {
    /*$today = date("Y-m-j").' 23:59:59';*/

    $dateToday = new DateTime();
    $date = $dateToday->format('D');
    if ($date == "Tue")
    {
        $dateToday->sub(new DateInterval('P2D'));
        $date = $dateToday->format('Y-m-d');
        echo $date;
    }


});

Route::get('/1123123123123123123123123121222222223', function () {
    $principal = explode("@",$_SERVER['REMOTE_USER']);
    $user_login = $principal[0];
    echo $user_login;


    $date = "2016-09-07";
    echo $day= date("j", strtotime($date));
    echo $month = date("m", strtotime($date));
    echo $year = date("Y", strtotime($date));


});


Route::get('/2341234123412341234', function () {
    /*$today = date("Y-m-j").' 23:59:59';*/


    $results = DB::connection('reminder')->select('select * from reminders');



    foreach ($results as $result) {
        if ($result->event == 1) {
            $test =  $result->event;
        }

    }
    return json_encode(array(
        "success" => true,
        "data" => $results,
        "time" =>  $dateToday = new DateTime(),
        "event" => $test
    ));


});

Route::get('/234523452345234523452345234532452345234525342345', function () {
    /*$today = date("Y-m-j").' 23:59:59';*/


    $results = DB::select('select * from reminders');


    foreach ($results as $result) {

        if ($result->id_type == 3) {

            $a_body = $result->a_body;
            Mail::raw($a_body, function($message)  use ($result)
            {
                $message->from('docs@teploset.ru', "Информационная рассылка");

                $message->to('isemin@teploset.ru')->subject($result->subject);;
            });

            echo "Your email has been sent successfully";

    /*        $days = $result->day;
            $hour = 13;
            $minute = 12;


                $data = array(
                    'a_body' => $result->a_body
                );

                Mail::send('emails.reminders', $data, function ($message) use ($result) {

                    $message->from('docs@teploset.ru', "Информационная рассылка");

                    $message->to('isemin@teploset.ru')->subject($result->subject);
                });

                return "Your email has been sent successfully";

    */
        } else if ($result->id_type == 2) {

            $a_body = $result->a_body;
            Mail::raw($a_body, function($message)  use ($result)
            {
                $message->from('docs@teploset.ru', "Информационная рассылка");

                $message->to('isemin@teploset.ru')->subject($result->subject);;
            });

            echo "Your email has been sent successfully";

        }

    }


});




Route::resource('reminder', 'Reminder');
Route::get('reminderbox', 'Reminder@reminderbox');
Route::post('reminderdell', 'Reminder@reminderdell');
Route::resource('weekly', 'Weekly');
Route::resource('monthly', 'Monthly');
Route::resource('exactdate', 'Exactdate');



Route::get('export/{startDate}/{endDate}', 'Otrsexport@exportExcel');

Route::get('exporttoday', 'Otrsexport@exportExcelToday');

Route::get('exportexcelyesterday', 'Otrsexport@exportExcelYesterday');


Route::get('/otrsmainsorting/{startDate}/{endDate}', function ($startDate, $endDate) {

    $date_start = $startDate.' 00:00:00';
    $date_end = $endDate.' 23:59:59';

    $results = DB::connection('otrs')->select('SELECT 
    DISTINCT ticket.id as id
	  ,ticket.tn 
        ,ticket.title
          ,IFNULL(CONCAT(article_body.a_body, article_body_PhoneCall.a_body_PhoneCall), IFNULL(article_body.a_body, article_body_PhoneCall.a_body_PhoneCall)) as a_body
            ,article_from.a_from
              ,ticket_type.name as ticket_type
                ,users.last_name as agent
                  ,IF(ticket_state.name = "closed successful",1,0) as state
                    ,time_sum.time_unit as time_unit
                      ,ticket.create_time as create_time
                        ,ticket_history_lock.lock_time as lock_time
                          ,ticket_history_end.end_time as end_time
                            ,article_body_newTicked.a_body_newTicked
						
    FROM ticket
    
    INNER JOIN queue ON queue.id = ticket.queue_id 
    
    INNER JOIN ticket_history as th ON ticket.id = th.ticket_id
    
    left join ticket_type on ticket.type_id = ticket_type.id
    
    left join users on ticket.user_id = users.id
    
    left join ticket_state on ticket.ticket_state_id = ticket_state.id
    left join (select time_accounting.ticket_id, sum(time_accounting.time_unit) as time_unit
        from time_accounting GROUP BY time_accounting.ticket_id) as time_sum  on time_sum.ticket_id = ticket.id
    
    left join (SELECT ticket_history.name as lock_name, ticket_history_type.name, max(ticket_history.create_time) as lock_time, ticket_history.ticket_id
    FROM ticket_history, ticket_history_type
    WHERE ticket_history.history_type_id = ticket_history_type.id
    AND ticket_history_type.name = "Lock" 
    GROUP BY ticket_history.ticket_id
    ORDER BY ticket_history.ticket_id DESC) as ticket_history_lock on ticket_history_lock.ticket_id = ticket.id
    
    left join (SELECT ticket_history.create_time, ticket_history.ticket_id, max(ticket_history.create_time) as end_time
    FROM ticket_history
    WHERE state_id IN (2, 3) AND history_type_id IN  (1, 27)
    GROUP BY ticket_history.ticket_id) as ticket_history_end on ticket_history_end.ticket_id = ticket.id
    
    left join (select article.ticket_id, GROUP_CONCAT(article.a_body) as a_body
    from article 
    where article.a_subject = "Закрытие заявки"
    GROUP BY article.ticket_id
    ORDER BY article.ticket_id DESC) as article_body on article_body.ticket_id = ticket.id
    
    left join (SELECT ticket_history.ticket_id, GROUP_CONCAT(article.a_body) as a_body_PhoneCall
    FROM ticket_history, ticket_history_type, article
    WHERE ticket_history.history_type_id = ticket_history_type.id
    AND ticket_history.article_id = article.id
    AND ticket_history_type.name = "PhoneCallCustomer"
    GROUP BY ticket_history.ticket_id
    ORDER BY ticket_history.ticket_id DESC) as article_body_PhoneCall on article_body_PhoneCall.ticket_id = ticket.id
    
    left join (SELECT ticket_history.ticket_id, GROUP_CONCAT(article.a_body) as a_body_newTicked
    FROM ticket_history, ticket_history_type, article
    WHERE ticket_history.history_type_id = ticket_history_type.id
    AND ticket_history.article_id = article.id
    AND (ticket_history_type.name = "EmailCustomer" OR ticket_history_type.name = "PhoneCallCustomer")
    GROUP BY ticket_history.ticket_id
    ORDER BY ticket_history.ticket_id DESC) as article_body_newTicked on article_body_newTicked.ticket_id = ticket.id

    left join (select min(ticket_history.id),article.id as article_id ,article.ticket_id as ticket_id ,article.a_from as a_from
    from article, ticket_history 
    where ticket_history.ticket_id = article.ticket_id 
    and ticket_history.article_id = article.id 
    and ticket_history.name = "%%"
    GROUP BY article.ticket_id) as article_from on article_from.ticket_id = ticket.id  
    
    WHERE 1 = 1
    AND (ticket.ticket_state_id IN ( 2, 3 ))
    AND th.history_type_id IN ( 1, 27 ) 
    AND th.state_id IN ( 2, 3 ) 
    AND th.create_time <=  ?
    AND th.history_type_id IN  (1, 27) 
    AND  th.state_id IN (2, 3) 
    AND th.create_time >=  ?
    ORDER BY users.last_name, ticket.id DESC', array($date_end, $date_start) );
    
    return json_encode(array(
        "success" => true,
        "data" => $results
    ));
});


Route::get('/otrsmain', function () {

    $date_start = date("Y-m-j").' 00:00:00';
    $date_end = date("Y-m-j").' 23:59:59';

    $results = DB::connection('otrs')->select('SELECT 
    DISTINCT ticket.id as id
	  ,ticket.tn 
        ,ticket.title
          ,IFNULL(CONCAT(article_body.a_body, article_body_PhoneCall.a_body_PhoneCall), IFNULL(article_body.a_body, article_body_PhoneCall.a_body_PhoneCall)) as a_body
            ,article_from.a_from
              ,ticket_type.name as ticket_type
                ,users.last_name as agent
                  ,IF(ticket_state.name = "closed successful",1,0) as state
                    ,time_sum.time_unit as time_unit
                      ,ticket.create_time as create_time
                        ,ticket_history_lock.lock_time as lock_time
                          ,ticket_history_end.end_time as end_time
                            ,article_body_newTicked.a_body_newTicked
						
    FROM ticket
    
    INNER JOIN queue ON queue.id = ticket.queue_id 
    
    INNER JOIN ticket_history as th ON ticket.id = th.ticket_id
    
    left join ticket_type on ticket.type_id = ticket_type.id
    
    left join users on ticket.user_id = users.id
    
    left join ticket_state on ticket.ticket_state_id = ticket_state.id
    left join (select time_accounting.ticket_id, sum(time_accounting.time_unit) as time_unit
        from time_accounting GROUP BY time_accounting.ticket_id) as time_sum  on time_sum.ticket_id = ticket.id
    
    left join (SELECT ticket_history.name as lock_name, ticket_history_type.name, max(ticket_history.create_time) as lock_time, ticket_history.ticket_id
    FROM ticket_history, ticket_history_type
    WHERE ticket_history.history_type_id = ticket_history_type.id
    AND ticket_history_type.name = "Lock" 
    GROUP BY ticket_history.ticket_id
    ORDER BY ticket_history.ticket_id DESC) as ticket_history_lock on ticket_history_lock.ticket_id = ticket.id
    
    left join (SELECT ticket_history.create_time, ticket_history.ticket_id, max(ticket_history.create_time) as end_time
    FROM ticket_history
    WHERE state_id IN (2, 3) AND history_type_id IN  (1, 27)
    GROUP BY ticket_history.ticket_id) as ticket_history_end on ticket_history_end.ticket_id = ticket.id
    
    left join (select article.ticket_id, GROUP_CONCAT(article.a_body) as a_body
    from article 
    where article.a_subject = "Закрытие заявки"
    GROUP BY article.ticket_id
    ORDER BY article.ticket_id DESC) as article_body on article_body.ticket_id = ticket.id
    
    left join (SELECT ticket_history.ticket_id, GROUP_CONCAT(article.a_body) as a_body_PhoneCall
    FROM ticket_history, ticket_history_type, article
    WHERE ticket_history.history_type_id = ticket_history_type.id
    AND ticket_history.article_id = article.id
    AND ticket_history_type.name = "PhoneCallCustomer"
    GROUP BY ticket_history.ticket_id
    ORDER BY ticket_history.ticket_id DESC) as article_body_PhoneCall on article_body_PhoneCall.ticket_id = ticket.id
    
    left join (SELECT ticket_history.ticket_id, GROUP_CONCAT(article.a_body) as a_body_newTicked
    FROM ticket_history, ticket_history_type, article
    WHERE ticket_history.history_type_id = ticket_history_type.id
    AND ticket_history.article_id = article.id
    AND (ticket_history_type.name = "EmailCustomer" OR ticket_history_type.name = "PhoneCallCustomer")
    GROUP BY ticket_history.ticket_id
    ORDER BY ticket_history.ticket_id DESC) as article_body_newTicked on article_body_newTicked.ticket_id = ticket.id

    left join (select min(ticket_history.id),article.id as article_id ,article.ticket_id as ticket_id ,article.a_from as a_from
    from article, ticket_history 
    where ticket_history.ticket_id = article.ticket_id 
    and ticket_history.article_id = article.id 
    and ticket_history.name = "%%"
    GROUP BY article.ticket_id) as article_from on article_from.ticket_id = ticket.id 

    WHERE 1 = 1
    AND (ticket.ticket_state_id IN ( 2, 3 ))
    AND th.history_type_id IN ( 1, 27 ) 
    AND th.state_id IN ( 2, 3 ) 
    AND th.create_time <=  ?
    AND th.history_type_id IN  (1, 27) 
    AND  th.state_id IN (2, 3) 
    AND th.create_time >=  ?
    
    ORDER BY users.last_name, ticket.id DESC', array($date_end, $date_start) );

    return json_encode(array(
        "success" => true,
        "data" => $results
    ));
});

