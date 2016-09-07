<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Mail;
use DB;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        // Commands\Inspire::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

        /*Запрос данных о всех напоминаниях*/
        $results = DB::select('select * from reminders where status = 1');

        foreach ($results as $result) {
            /**
             * Точная дата напоминание
             **/
            if ($result->id_type == 4) {

                $days = $result->day;
                $month = $result->month;
                $hour = $result->hour;
                $minute = $result->minute;

                $schedule->call(function () use ($result) {
                    $data = array(
                        'a_body' => $result->a_body
                    );

                    Mail::send('emails.reminders', $data, function ($message) use ($result) {

                        $message->from('docs@teploset.ru', "Информационная рассылка");

                        $message->to('isemin@teploset.ru')->subject("Напоминание: " + $result->subject);
                    });

                    echo "Your email has been sent successfully";

                })->cron("$minute $hour $days $month * *");
            }

            /**
             * Ежемесячное напоминание
             **/
            if ($result->id_type == 3) {

                $days = $result->day;
                $hour = $result->hour;
                $minute = $result->minute;

                $schedule->call(function () use ($result) {
                    $data = array(
                        'a_body' => $result->a_body
                    );

                    Mail::send('emails.reminders', $data, function ($message) use ($result) {

                        $message->from('docs@teploset.ru', "Информационная рассылка");

                        $message->to('isemin@teploset.ru')->subject("Ежемесячное напоминание: " + $result->subject);
                    });

                    echo "Your email has been sent successfully";

                })->monthlyOn($days, "$hour:$minute");
            }

            /**
             * Еженедельное напоминание
             **/
            if ($result->id_type == 2) {

                $weekday= $result->weekday;
                $hour = $result->hour;
                $minute = $result->minute;

                $schedule->call(function () use ($result) {
                    $data = array(
                        'a_body' => $result->a_body
                    );

                    Mail::send('emails.reminders', $data, function ($message) use ($result) {

                        $message->from('docs@teploset.ru', "Информационная рассылка");

                        $message->to('isemin@teploset.ru')->subject("Еженедельное напоминание: " + $result->subject);
                    });

                    echo "Your email has been sent successfully";

                })->weekly()->days($weekday)->at("$hour:$minute");
            }

        }





        /*$schedule->call(function()
        {
            $data = array(
                'name' => 123,
                'order' => 123,
                'normbalance' => 123
            );

            Mail::send('emails.issuecartridge', $data, function($message)
            {
                $message->from('docs@teploset.ru', "Информационная рассылка");

                $message->to('isemin@teploset.ru')->subject("Информационная рассылка о печатающий технике");
            });

            return "Your email has been sent successfully";

        })->everyMinute();*/

   /*     $everyMinute = 1;
        if ($everyMinute == 1) {
            $schedule->call(function () {
                $data = array(
                    'name' => 1212312312313,
                    'order' => 121231231233,
                    'normbalance' => 1212312313
                );

                Mail::send('emails.issuecartridge', $data, function ($message) {
                    $message->from('docs@teploset.ru', "Информационная рассылка");

                    $message->to('isemin@teploset.ru')->subject("Информационная рассылка о печатающий технике");
                });

                return "Your email has been sent successfully";

            })->everyMinute();
        }*/
    }
}
