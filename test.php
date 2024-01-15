<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="description" content="Smarthr - Bootstrap Admin Template">
    <meta name="keywords"
          content="admin, estimates, bootstrap, business, corporate, creative, management, minimal, modern, accounts, invoice, html5, responsive, CRM, Projects">
    <meta name="author" content="Dreamguys - Bootstrap Admin Template">
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>CRM</title>
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('public/assets/img/logo/favicon.png') }}">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('public/assets/css/bootstrap.min.css') }}">

    <link rel="stylesheet" href="{{ asset('public/assets/css/accounts-style.csss') }}">
    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="{{ asset('public/assets/css/font-awesome.min.css') }}">
    <!-- Lineawesome CSS -->
    <link rel="stylesheet" href="{{ asset('public/assets/css/line-awesome.min.css') }}">


    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.4/css/jquery.dataTables.css">



    <!-- Select2 CSS -->
    <link rel="stylesheet" href="{{ asset('public/assets/css/select2.min.css') }}">
    <!-- Datetimepicker CSS -->

    <link rel="stylesheet" href="{{ asset('public/assets/css/bootstrap-datetimepicker.min.css') }}">
    <!-- Chart CSS -->
    <link rel="stylesheet" href="{{ asset('public/assets/plugins/morris/morris.css') }}">
    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('public/assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('public/assets/css/table-style.css') }}">




</head>

<body>


<div class="container-fluid">
    {{--        <button class="btn btn-success mt-3" id="fff">Print</button>--}}
</div>
<div class="container-fluid mt-4" id="printTable">
    <div class="text-center">
        <h4 class="daily">Attendance Sheet Month Of Jun 2022</h4>
    </div>

    {{--        <table class="table-responsive">--}}
        {{--            <tr>--}}
            {{--                <td>#SR</td>--}}
            {{--                <td class="name">Name</td>--}}
            {{--                <td class="mm">01 </td>--}}
            {{--                <td class="mm">02 </td>--}}
            {{--                <td class="mm">03 </td>--}}
            {{--                <td class="mm">04 </td>--}}
            {{--                <td class="mm">05 </td>--}}
            {{--                <td class="mm">06 </td>--}}
            {{--                <td class="mm">07 </td>--}}
            {{--                <td class="mm">08 </td>--}}
            {{--                <td class="mm">09 </td>--}}
            {{--                <td class="mm">10 </td>--}}
            {{--                <td class="mm">11 </td>--}}
            {{--                <td class="mm">12 </td>--}}
            {{--                <td class="mm">13 </td>--}}
            {{--                <td class="mm">14 </td>--}}
            {{--                <td class="mm">15 </td>--}}
            {{--                <td class="mm">16 </td>--}}
            {{--                <td class="mm">17 </td>--}}
            {{--                <td class="mm">18 </td>--}}
            {{--                <td class="mm">19 </td>--}}
            {{--                <td class="mm">20 </td>--}}
            {{--                <td class="mm">21 </td>--}}
            {{--                <td class="mm">22 </td>--}}
            {{--                <td class="mm">23 </td>--}}
            {{--                <td class="mm">24 </td>--}}
            {{--                <td class="mm">25 </td>--}}
            {{--                <td class="mm">26 </td>--}}
            {{--                <td class="mm">27 </td>--}}
            {{--                <td class="mm">28 </td>--}}
            {{--                <td class="mm">29 </td>--}}
            {{--                <td class="mm">30 </td>--}}
            {{--                <td class="mm">31 </td>--}}
            {{--            </tr>--}}

        {{--            @isset($data['dept'])--}}
        {{--                @foreach($data['dept'] as $dept)--}}
        {{--                    <tr>--}}
            {{--                        <td colspan="32" class="font-weight-bold text-center"><h4>{{$dept->departments}}</h4></td>--}}
            {{--                    </tr>--}}
        {{--            <tr>--}}
            {{--                <td class="text-center">1</td>--}}

            {{--                <td class="name">Zaeem Asif</td>--}}

            {{--                @for($i=0;$i<31;$i++)--}}
            {{--                <td>12:50 </td>--}}
            {{--                    @endfor--}}

            {{--            </tr>--}}
        {{--                @endforeach--}}
        {{--            @endisset--}}

        {{--        </table>--}}


    <table class="table table-striped custom-table table-nowrap mb-0 table-responsive">
        <thead>
        <tr>
            <th>Employee</th>
            <th>
                <?php

                $data['month'] ? ($month = $data['month']) : ($month = date('m'));
                $data['year'] ? ($year = $data['year']) : ($year = date('Y'));

                $a_date = $year . '-' . $month;
                $lastDayOfThisMonth = date('t', strtotime($a_date));



                $month_name = date('F', mktime(0, 0, 0, $month, 10));
                ?>
            </th>
            @for ($i = 1; $i <= $lastDayOfThisMonth; $i++)
            <th>{{ $i }}</th>
            @endfor
        </tr>
        </thead>
        <tbody>
        @php $c=0; @endphp
        @isset($data['employee'])
        @foreach ($data['employee'] as $emp)
        @php $c++; @endphp
        <tr>
            <td>
                <h2 class="table-avatar">{{$emp->name}}</h2>
            </td>

            <td>
                <?php
                for ($k = 1; $k <= $lastDayOfThisMonth; $k++) {
                    $att = App\Models\Attendance::where([['emp_id', $emp->id], ['attendance_date', $k], ['attendance_month', $month], ['attendance_year', $year]])->get();

                    if ($att->count() > 0) {
                        echo '<td>';
                        foreach ($att as $value => $att) {
                            $attDate = date('d', strtotime($att->date));
                            $attDay = date('d', strtotime($att->date));

                            if ($k == $attDay) {
                                if ($att->status == 'Present') {
                                    echo date('H:i:s',strtotime($att->created_at));
                                }
                                if ($att->status == 'Absent') {
                                    echo '-';
                                }
                            }

                            echo '</td>';
                        }
                    } else {
                        $date = date_create(date($k . 'M-Y'));
                        $dayName = date_format($date, 'l');

                        if ($dayName == 'Sunday') {
                            echo '<td style="color: red">' . $dayName . '</td>';
                        } else {
                            echo '<td>-</td>';
                        }
                    }
                }
                ?>
            </td>
        </tr>
        @endforeach
        @endisset
        </tbody>
    </table>
</div>


</body>

</html>
