<!DOCTYPE html>
<html>
    <head>
        <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">

        <style>
            html, body {
                height: 100%;
            }

            body {
                margin: 0;
                padding: 0;
                width: 100%;
                display: table;
                font-weight: 100;
                font-family: 'Lato';
            }

            .container {
                /*text-align: left;*/
                display: table-cell;
                vertical-align: middle;
            }

            .content {
                /*text-align: left;*/
                display: inline-block;
            }

            .title {
               font-family:calibri, sans-serif;
            }
            .text {
                font-family:calibri, sans-serif;
                font-size:80%;
                margin:3pt 0 0 0;
            }
        </style>
    </head>
    <body>
        <div>{{$a_body}}</div>
        <div class="container">
            <div class="content">
                <div class="title">Внимание!</div>
            </div>
        </div>
    </body>
</html>
