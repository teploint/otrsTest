<?php
/**
 * Created by PhpStorm.
 * User: semin.i
 * Date: 10.06.2016
 * Time: 13:33
 */



Route::get('/otrsmainsorting/{startDate}/{endDate}', function ($startDate, $endDate) {

    $date_start = $startDate.' 00:00:00';
    $date_end = $endDate.' 23:59:59';

    $results = DB::select('SELECT 
    DISTINCT ticket.id as id
	  ,ticket.tn 
        ,ticket.title
          ,IFNULL(article_body.a_body, article_body_PhoneCall.a_body_PhoneCall) as a_body
            ,article_from.a_from
              ,concat(users.first_name," ", users.last_name) as agent
                ,IF(ticket_state.name = "closed successful",1,0) as state
                  ,time_sum.time_unit as time_unit
                    ,ticket.create_time as create_time
                      ,ticket_history_lock.lock_time as lock_time
                        ,ticket_history_end.end_time as end_time 
						
    FROM ticket

    INNER JOIN queue ON queue.id = ticket.queue_id 
    
    INNER JOIN ticket_history as th ON ticket.id = th.ticket_id
    
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
    where article.a_subject = "Закрыть"
    GROUP BY article.ticket_id
    ORDER BY article.ticket_id DESC) as article_body on article_body.ticket_id = ticket.id
    
    left join (SELECT ticket_history.ticket_id, GROUP_CONCAT(article.a_body) as a_body_PhoneCall
    FROM ticket_history, ticket_history_type, article
    WHERE ticket_history.history_type_id = ticket_history_type.id
    AND ticket_history.article_id = article.id
    AND ticket_history_type.name = "PhoneCallCustomer"
    GROUP BY ticket_history.ticket_id
    ORDER BY ticket_history.ticket_id DESC) as article_body_PhoneCall on article_body_PhoneCall.ticket_id = ticket.id


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
    ORDER BY concat(users.first_name," ", users.last_name), ticket.id DESC', array($date_end, $date_start) );

    return json_encode(array(
        "success" => true,
        "data" => $results
    ));
});