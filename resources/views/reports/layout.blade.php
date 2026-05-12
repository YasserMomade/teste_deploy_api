<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="utf-8">
    <style>
        .header {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }

        .header-logo {
            display: table-cell;
            width: 170px;
            vertical-align: top;
            padding-right: 20px;
        }

        .header-logo img {
            width: 155px;
        }

        .header-info {
            display: table-cell;
            vertical-align: top;
            font-size: 10.5px;
            color: #222;
            line-height: 1.65;
        }

        .header-divider {
            margin-bottom: 28px;
        }

        .report-title {
            font-size: 13px;
            font-weight: bold;
            color: #222;
            margin-bottom: 5px;
        }

        .report-meta {
            font-size: 10px;
            color: #666;
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .summary-wrap {
            width: 100%;
            border-collapse: separate;
            border-spacing: 5px;
            margin-bottom: 20px;
            display: table;
        }

        .summary-box {
            display: table-cell;
            border: 1px solid #ddd;
            padding: 8px 10px;
            text-align: center;
            vertical-align: middle;
        }

        .s-label {
            font-size: 8px;
            text-transform: uppercase;
            color: #888;
            letter-spacing: .04em;
            display: block;
            margin-bottom: 3px;
        }

        .s-value {
            font-size: 18px;
            font-weight: bold;
            color: #962479;
            display: block;
        }

        .s-value.green {
            color: #2e7d2e;
        }

        .s-value.red {
            color: #b52222;
        }

        .section-title {
            font-size: 11px;
            font-weight: bold;
            color: #962479;
            border-bottom: 1px solid #e0c8db;
            padding-bottom: 3px;
            margin-bottom: 8px;
            margin-top: 16px;
        }

        table.dt {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        table.dt thead tr {
            background-color: #962479;
            color: #fff;
        }

        table.dt thead th {
            padding: 5px 7px;
            font-size: 9px;
            font-weight: bold;
            text-align: left;
        }

        table.dt tbody td {
            padding: 4px 7px;
            font-size: 9px;
            border-bottom: 1px solid #eee;
            vertical-align: top;
        }

        table.dt tbody tr:nth-child(even) {
            background-color: #faf5f9;
        }

        .badge {
            display: inline-block;
            padding: 1px 6px;
            border-radius: 8px;
            font-size: 8px;
            font-weight: bold;
        }

        .badge-paid {
            background: #e6f4e6;
            color: #2a6e2a;
        }

        .badge-pendent {
            background: #fef7e0;
            color: #8a6200;
        }

        .badge-faild {
            background: #fdecea;
            color: #a11c1c;
        }

        .badge-good {
            background: #e6f4e6;
            color: #2a6e2a;
        }

        .badge-medium {
            background: #fef7e0;
            color: #8a6200;
        }

        .badge-bad {
            background: #fdecea;
            color: #a11c1c;
        }

        .badge-critical {
            background: #3d0000;
            color: #fff;
        }

        .empty-state {
            font-size: 9.5px;
            color: #aaa;
            font-style: italic;
            padding: 6px 0 6px 4px;
        }

        .exc-header {
            display: table;
            width: 100%;
            margin-bottom: 5px;
            border-bottom: 1px solid #e0c8db;
            padding-bottom: 3px;
        }

        .exc-header-title {
            display: table-cell;
            font-size: 11px;
            font-weight: bold;
            color: #962479;
            vertical-align: middle;
        }

        .exc-count {
            display: table-cell;
            text-align: right;
            vertical-align: middle;
        }

        .exc-count span {
            background: #fdecea;
            color: #a11c1c;
            font-size: 8.5px;
            font-weight: bold;
            padding: 2px 8px;
            border-radius: 8px;
        }

        .exc-count span.zero {
            background: #e6f4e6;
            color: #2a6e2a;
        }

        .page-footer {
            position: fixed;
            bottom: 0;
            left: 38px;
            right: 38px;
        }

        .footer-line-purple {
            height: 6px;
            background-color: #962479;
        }

        .footer-line-lime {
            height: 2.5px;
            background-color: #c5d22d;
            margin-bottom: 4px;
        }

        .footer-text {
            font-size: 7.5px;
            color: #333;
            padding: 0 38px 5px 38px;
            line-height: 1.4;
        }

        .footer-text strong {
            text-transform: uppercase;
            letter-spacing: .04em;
        }


        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #222;
            background: #fff;

            margin-top: 26px;
            margin-right: 38px;
            margin-bottom: 20px;
            margin-left: 38px;
        }
    </style>
</head>

<body>

    <div class="page-footer">
        <div class="footer-line-purple"></div>
        <div class="footer-line-lime"></div>
        <div class="footer-text">
            <strong>Abrangência:</strong>
            Maputo, Matola, Xai-Xai, Inhambane, Maxixe, Vilanculo, Beira, Chimoio, Tete, Quelimane, Mocuba,
            Nampula, Nacala, Pemba, Lichinga, Johannesburg e Lisboa
        </div>
    </div>

    <div class="body-content">

        <div class="header">
            <div class="header-logo">
                <img src="data:image/svg+xml;base64,PHN2ZyBpZD0iTGF5ZXJfMSIgZGF0YS1uYW1lPSJMYXllciAxIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyMDMzLjMzIDQ4NS45MSI+PGRlZnM+PHN0eWxlPi5jbHMtMXtmaWxsOiM5NjI0Nzk7fS5jbHMtMSwuY2xzLTJ7ZmlsbC1ydWxlOmV2ZW5vZGQ7fS5jbHMtMntmaWxsOiNjNWQyMmQ7fTwvc3R5bGU+PC9kZWZzPjxwYXRoIGNsYXNzPSJjbHMtMSIgZD0iTTEzNDguMzMsMTA3Mi4xM2MtNjIuODUsNS4wNy0zNS44Niw3Ni41OS00MC45NCwxMTUuODdoMjIuOWMxLjM1LTI0LjM4LDAtNDguNjQuNjEtNzMtMS41OC0yNi40NywyOC43NC0yMC41LDQ4LjUyLTIxdi0yMS44OGMtMTEsMC0yMS4wNy0uMjgtMzEuMDkuMDZabS02NjIuODcsNzguNDhjLS42OS00Ni43NS03LjI3LTYxLjQ1LDUyLjE0LTU2LjY2di0yMS44MmMtOTcuNjUtMTAuNTUtNzUsNTMuMjctNzUuMzEsMTE2aDIzLjE3di0zNy40OVptNzguNDItOS43MmMwLDM1Ljc5LDIwLjkzLDUyLjE3LDU5LjY4LDQ2LjQzdi0yMC40MWMtMTItLjU3LTMyLjQyLDMuNjEtMzQuMi0xMS4xMS0uOTItMjAuODMtLjMxLTQxLjctLjMxLTYzLjVoMzQuNDJ2LTIwLjQySDc4Ny43di00NC42Mkg3NjMuODh2MTEzLjYzWm0yMTEuODUtMzYuMDVjLTMuNzEtMzguODItNTUuMzQtMzMuNDctODYuNDgtMzIuNTgtMjIuNDcuNzktMzkuMDYsMTYuNjQtMzkuNjksMzcuMzYtMi42MSw1Ni4zNS0yLjgzLDg0LjI5LDY0Ljc0LDc4LjU2bDI1LjU3LTIxLjM4Yy03LjM2LTIuNy03MC43NSwxMC42NC02Ni4zNy0xOS4zNywyLjQ5LTE3LjQtMTAuMDctNTEuOTMsMTguNDEtNTMuMjQsMjUuNjUtLjEzLDY4LjA4LTcuODQsNjAuNTEsMjguNjV2NjUuMzJIOTc2YzAtMjguNDcuODMtNTUuOTMtLjMtODMuMzJabS0zNjMuNTUsMjYuMjRjLjM5LTE1Ljk1LDIuNjgtMzguMzMtMjEuMjgtMzctMTkuNTYsMS44Mi01Ny4yMS04LjkzLTU4LDE4LjM2LDEuOTQsMTguMzQtOS44MSw1My4xOCwyMC4xNiw1NC4zNCwzNy43MS0xLDY0LjMsOS4xMiw1OS4wOC0zNS42NlptLTM5LjYxLDU3LjVjLTI5LjIsMy40OC02My40LTYuNDItNjMuMzgtMzguMjMtMS4yNi0zMC4xNS01LjQ3LTc2LjI2LDM5LjU2LTc4LDMzLjMyLTEuNDMsODUuMi02LDg3LDM2LjM5LDQuMjUsNDYuMjUuMyw4Ny4zMy02My4yLDc5Ljg5Wm02NDYtOTQuNTRhLjQzLjQzLDAsMCwwLDAsMGMtMTUuODYtLjI3LTQwLjE5LTIuOTUtMzkuODIsMTcuNjgsMi4xNiwxOC4wNi0xMC4yNCw1My40NCwxOS4yNiw1NC45MSwxOS43OC0xLjgsNTgsOS4xOSw1OS44NC0xNy44NS4zOC0zMS44NCw4Ljc3LTYxLjM2LTM5LjI3LTU0Ljc5Wm0tLjQxLDk0LjU1Yy02NS40Niw3LjkzLTY3LjQ4LTM2LjU2LTYyLjgtODMuMTIsNC00MC4xNSw1NS44NS0zNCw4Ny44NS0zMy4wOCw0NCwyLjM1LDM5LjYyLDQ4LjMzLDM4LjQ1LDc4LDAsMzEuNzMtMzQuMjcsNDEuNzItNjMuNSwzOC4yWm0tODA0LjE2LDBjMjguMDgsMi4zMyw2OC4wOC0xLjM4LDY5LjQzLTM2Ljk0LDIuNTUtMzEuMzYsNC45My03OC45LTQxLjgzLTc5LjUtMzguNjYtMi4xMS04OC44Mi0yLjUxLTg1LjY2LDQ1LC4yOSwzOC43NC0uODEsNzcuNS41NSwxMTYuMjJoMjMuOTNjMC0zOC42OC0uMDctNzcuNDUsMC0xMTYuMTEsMC0xOC4zMyw1LjUxLTIzLjE4LDI1Ljc2LTIzLjI0LDU1LjA4LTMuNzEsNTYuMDksNC43OCw1My40Nyw1Mi41NCwzLjkxLDMxLjQzLTU5LjExLDE3LjQ0LTY4LjMyLDIwLjI5TDQxNCwxMTg4LjU1Wk0xMTA0LjcxLDEwOTRjLTI0LjE5LDQuNDQtNzUuMTYtMTMuNDctNzguOTQsMTcuNjgsMS40OCwxNy41NS05LjUyLDUyLjU2LDE4LDU1LDgwLjE0LDcsNTguMzItMTQuMDgsNjEtNzIuNjlabS4zNy0yMS44MXYtNDUuMDhoMjMuNzljMS42MiwzOC42My0uNjIsNzcuNDUuODEsMTE2LjExLjYyLDQ5LjgzLTQ2LjUyLDQ2LjQ4LTg0LjU3LDQ1LjMzLTI0LjA5LS4xMy00Mi4yMi0xNS43MS00My4yMy0zNy44MS0yLjE4LTMxLjE4LTQuNDItNzguMDksNDEuOTQtNzguNTVDMTA2My4yNCwxMDcxLjcsMTEwNS4wOCwxMDcyLjE1LDExMDUuMDgsMTA3Mi4xNVoiIHRyYW5zZm9ybT0idHJhbnNsYXRlKC02Mi45MiAtODMzLjc3KSIvPjxwYXRoIGNsYXNzPSJjbHMtMSIgZD0iTTE3MzUuNTUsMTAyNy4wN2gtMTRsLTEzLjMyLDE3LjQ1aDE0bDEzLjMyLTE3LjQ1Wm0tMTIyLjk0LDQ3LjU2aC0xMy40NXYxMTcuODloMTMuNDVWMTA3NC42M1ptMzE4LjIyLTEuNTdjLTUsLjIyLTksLjM5LTEyLjg1LjU3VjExOTIuOGgxMi44NVYxMDczLjA2Wm0tNzIuNDUuNjFjLTcwLjY5LTMtNDIuMTEsNzguMTEtNDcuMjcsMTE5LjA2aDEzLjYzYzAtMjUuNzUtLjEtNTAuOSwwLTc2LjU2LTIuMTItMzMuNDIsMzMuMi0yOS43Miw1OS4zOC0yOS4zLjI3LTQuNTYuNDgtOCwuOC0xMy4yNC05LjgzLDAtMTguMTktLjA3LTI2LjU0LDBabS04MS4yNiw0My41NWM1LjcyLTUwLjU2LTU3LjUyLTQ1Ljg2LTk1LjE4LTQzLjIyLTQ0LjQzLDQtMzQuODIsNTMtMzUuMTQsODIuNDYtLjU5LDQ1LjA4LDY2LjE2LDM1Ljc2LDg5Ljc3LDM2LjEybDE5LjMtMTIuNTJzLTQ0LjUxLjIxLTY2LDBjLTE3LjUtLjE3LTI4LjYtMTAuNTgtMjguNzctMjYuNjQsMS44My0yMy41LTEwLjYtNjUuNzIsMjcuNDgtNjYuNzMsMzEuNDEuODcsNzguODctOS43OCw3NC4xMywzMy41MywwLDIzLjkzLDAsNDcuODYsMCw3Mi4zNGgxNC4zOWMwLTI1LjQsMC01MC4zNywwLTc1LjM0Wm0yMDIuMjksMTYuMTVjLTcuNDEsNTQuNjQsMjkuMjQsNDcuMjcsNzMuNiw0Ni42NywzOC44Ny0xLjQ2LDI2LjUzLTQ0LjUzLDI4LTY4LjU5LTItMzUuMjgtNDkuNTYtMjMuMTItNzUuNDItMjQuNi0yNy45LjI3LTI3LjQsMjcuNDItMjYuMiw0Ni41MlptNTAuNDQsNTkuNzhjLTI4LjE1LDIuNzItNjMuMjEtMy40Mi02NC42NS0zNC44MS0uMzItMjkuODgtMTAuMTgtODAuNDUsMzUuMjgtODQuMzUsMzQuMzItLjg4LDkyLjI0LTkuODEsOTQuNzYsMzUuMTMsMi44Niw0OS44Miw0LjQyLDkxLjU2LTY1LjM5LDg0Wm0tNDc3LjgtMTA2LjM3Yy03Ni40NywzLjU1LTExMC45LTIyLjUtMTAxLjc0LDY4LjU1LDEuNzcsMzQuNjIsNDcuOTQsMjMuMTgsNzMuNDMsMjQuNzQsNDcuMTItNCwyMi43Ny02MywyOC4zMS05My4yOVptLjE0LTEzLjY5di00NmgxNC4yMWMtLjA2LDQyLjcxLjEzLDg0LDAsMTI2LjQxLjU3LDQ1LTUyLjM2LDQwLjU5LTg3LjQsMzkuNjQtNDkuMzcuODQtNDUuNzEtNDkuNi00My40Ny04Mi4wOUMxNDM5Ljg2LDEwNTYuNDMsMTUxMC4zOCwxMDc2LjYzLDE1NTIuMTksMTA3My4wOVoiIHRyYW5zZm9ybT0idHJhbnNsYXRlKC02Mi45MiAtODMzLjc3KSIvPjxwYXRoIGNsYXNzPSJjbHMtMSIgZD0iTTMzOC44Nyw4NjcuN2MtMzcsMzMuMzYtODAuNDksNTctMTI5LjY2LDczLjU1LTUuNTMsMS44Ni02LjgsNS02LjIxLDkuNTQsMS4wNyw4LjQyLDIuNjQsNTEuMTIsMi42NCw2NC44NywxNC4wNS03LjQ2LDM0LjMxLTkuNDcsNDMuNi0yMC4yNCw2LjY4LTE3LjItMi40Mi00Ny4yMiwyNi41Ny00OS41NiwxNi43NC0zLjMyLDM2Ljc1LTMuNzcsNTEuNzMtMTAuMywyLjI0LTcuMDYsNC0xNC4yMiw2LjM4LTIyLjkxLTE0LjU5LDcuMjYtMjcuMjYsMTMuNDctNDEuNjIsMTYuNTcsNTAuNDctMzYuNTcsNDMuMTctMzEuNTUsNDYuNTctNjEuNTJabTgzLjY5LDQxLjc3QzQwNC4zOSw5NDcuNDcsNDI3LDkzMiwzNTEuNjcsOTY0YTM2Ny45LDM2Ny45LDAsMCwwLDUxLjY2LTkuMjljLTE4LjE5LDI2LDMsMTIuMzUtNjIsNDEuODhhMzYzLjY0LDM2My42NCwwLDAsMCwzNy41OC01Ljc4Yy0xOC42MSwyNC41Mi0xMy44MiwxOC4zNS02MC43MywzNi40MiwxMy44NywwLDI5LTIuOSwyOS4yMi0yLjYtMjMuMTQsMTguNTQtNTEuNjMsMzMuNzgtODIsNDEuNjcsMCwwLC42OCwzNi4yMS42NCw1MiwwLDE0LjU5LTguMTEsMjUuMDgtMjMuNTQsMzEuMjJxLTc0Ljc2LDI5Ljc1LTE0OS43LDU5LjE0Yy0xMi4xNCw0Ljc4LTE3Ljc3LDEuOC0xNy44MS05LjUxLS4wOC0zMy40LS4xOC02Ni43OS4wNi0xMDAuMTguMDgtMTMuMDYsNy4zMS0yMy4zMywyMS4xMi0yOC45Miw0OS4yOC0xOS4xNyw5OC00MC4zNiwxNDcuODctNTcuODktMTYsNDEuNDEtNDMuMyw4MS40My03NS42NSwxMTUuMjMtMTguMjQtNy4xNi02NC4yMi0yOS43NC02OC4xOC0zMS4yOC45NCwxLDYzLjExLDM4LDcwLjcxLDQwLjEzLDQ3LjkyLTQ2LjYsODIuNTYtMTA0LjExLDk0LjQ5LTE2NS4yLDIuNzEtMTMuODgsMi40Ny0xMy45NCwxOS0xNi4zMyw0OS42OS03LjIyLDk2LjM5LTIwLjU3LDEzOC4yMS00NS4yN1ptLTc1LTc1LjdhMjQ5LjQ5LDI0OS40OSwwLDAsMS03LjU0LDk5LjljMzguNC0xMiw3NC43My0yNC41NCwxMDEtNTIuNjItMTYuNDIsNzkuNzItNjguNiwxNjMuMzktMTYzLjU5LDE5MS42Ni0uNzUsMjAuMDgsMTAuNTgsNzQuODUtMzAuNjMsODZxLTcyLjA2LDI4LjUxLTE0NC4xNCw1N2MtMTYuNjEsOC43NC0zOS4zNSw1Ljc3LTM5LjIyLTEzLjUyLS41NC0zNS4zMS0uODYtNzAuNjYuMTUtMTA2LTEuODQtMzkuNzgsODkuMy01NC45LDEyNC4zNi03My40NCwxNC4xNS0zLjc3LDEuNTktNTguMiwzLjc5LTcyLjIzLS41MS05LjA2LDMuNjktMTUsMTQuNTMtMTguNTJDMjYyLjM1LDkxMiwzMjIuMDcsODgyLjgzLDM0Ny41Niw4MzMuNzdaIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSgtNjIuOTIgLTgzMy43NykiLz48cGF0aCBjbGFzcz0iY2xzLTIiIGQ9Ik0xMDczLjA1LDEyOTQuNWMyLTE5LjM1LTI2LjQ1LTE5LjM1LTI0LjQzLDAtMiwxOS41NiwyNi40OCwxOS41NiwyNC40MywwWm0xMS45MiwwYzIuNDEsMzEuNzctNTAuNTgsMzEuNzEtNDguMjcsMEMxMDM0LjQ2LDEyNjMsMTA4Ny4yNCwxMjYzLDEwODUsMTI5NC41WiIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoLTYyLjkyIC04MzMuNzcpIi8+PHBhdGggY2xhc3M9ImNscy0yIiBkPSJNMTEyNy4xNywxMzEwYzYuNjQuODUsMTIuMjQtMy44Miw2LjU5LTguMy01LjcyLTMuNzItMTcuNzQtNC45MS0yMC44Ni0xMi4xNC03LjMzLTE4LDE4LjU1LTIyLjYyLDMyLjM1LTE3bC0yLjExLDguNjVjLTUuMzctMy4xNi0yNi4xMS0zLjgtMTcuODksNiw1LjUzLDMuNDQsMTQuMiw0LjQ2LDE4LjgsOC45MiwxNS40OCwxOS44MS0xOC4wNywyNy41Mi0zMy4zMiwyMGwyLTguOTJBNDAuMjMsNDAuMjMsMCwwLDAsMTEyNy4xNywxMzEwWiIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoLTYyLjkyIC04MzMuNzcpIi8+PHBhdGggY2xhc3M9ImNscy0yIiBkPSJNMTE4Ny4wNywxMjkwYy44OS0xNC4zNi0yMS40NS0xMy42My0yMS42NCwwWm0tMzMuNzUsNC43N2MtMS0zMy42Myw1MS45MS0zMS43OCw0NS4xOSwzLjA5aC0zMy4xN2MuMzgsMTMuMzUsMTguNSwxMy4yNywyOC41Niw5LjQ1bDEuNTMsOC43NEMxMTc3LjE4LDEzMjMuNjYsMTE1MS40OSwxMzE2LDExNTMuMzIsMTI5NC43N1oiIHRyYW5zZm9ybT0idHJhbnNsYXRlKC02Mi45MiAtODMzLjc3KSIvPjxwYXRoIGNsYXNzPSJjbHMtMiIgZD0iTTEyNDkuODUsMTMxNi4yM2MtNDQuNTEsMTEuMzktNDMuMjMtMTIuODYtNDEuODItNDQuNzdoMTEuNjRjMS4zMSwxMy4xMi03LjEyLDQ1LDE4LjU1LDM3LjM1di0zNy4zNWgxMS42M1oiIHRyYW5zZm9ybT0idHJhbnNsYXRlKC02Mi45MiAtODMzLjc3KSIvPjxwYXRoIGNsYXNzPSJjbHMtMiIgZD0iTTEyNzkuNDYsMTI5NC41OWMtMS4yNi0xOC44OCwyMC43NC0yOS41OSwzOC4zNi0yMS45bC0yLjUsOC43NWMtMzMtMTIuODQtMzEuNDgsNDAuMTYsMS42NCwyNi4yMmwxLjYzLDguODNDMTMwMC44MSwxMzIzLjgzLDEyNzcuNTksMTMxNC4zNywxMjc5LjQ2LDEyOTQuNTlaIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSgtNjIuOTIgLTgzMy43NykiLz48cGF0aCBjbGFzcz0iY2xzLTIiIGQ9Ik0xMzU3LjcyLDEyOTQuNWMyLTE5LjM1LTI2LjQ0LTE5LjM1LTI0LjQyLDAtMiwxOS41NiwyNi40OCwxOS41NiwyNC40MiwwWm0xMS45MywwYzIuNDEsMzEuNzctNTAuNTgsMzEuNzEtNDguMjcsMEMxMzE5LjE0LDEyNjMsMTM3MS45MiwxMjYzLDEzNjkuNjUsMTI5NC41WiIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoLTYyLjkyIC04MzMuNzcpIi8+PHBhdGggY2xhc3M9ImNscy0yIiBkPSJNMTQwNy44NywxMjgxLjA4Yy00LjcxLTEuNTMtMTItMS45NC0xNy0uNDR2MzdoLTExLjYydi00NGM4LjktMywyMC45NC00LjQ3LDMwLjYzLTEuNTlaIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSgtNjIuOTIgLTgzMy43NykiLz48cGF0aCBjbGFzcz0iY2xzLTIiIGQ9Ik0xNDQ0LjI1LDEyODEuMDhjLTQuNzItMS41My0xMi0xLjk0LTE3LS40NHYzN2gtMTEuNjN2LTQ0YzguOTEtMywyMS00LjQ3LDMwLjY2LTEuNTlaIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSgtNjIuOTIgLTgzMy43NykiLz48cGF0aCBjbGFzcz0iY2xzLTIiIGQ9Ik0xNDgyLjksMTI5MGMuOS0xNC4zNi0yMS40NS0xMy42My0yMS42MywwWm0tMzMuNzQsNC43N2MtMS0zMy42Myw1MS45MS0zMS43OCw0NS4xOCwzLjA5aC0zMy4xN2MuMzgsMTMuMzUsMTguNSwxMy4yNywyOC41Niw5LjQ1bDEuNTQsOC43NEMxNDczLDEzMjMuNjYsMTQ0Ny4zMywxMzE2LDE0NDkuMTYsMTI5NC43N1oiIHRyYW5zZm9ybT0idHJhbnNsYXRlKC02Mi45MiAtODMzLjc3KSIvPjxwYXRoIGNsYXNzPSJjbHMtMiIgZD0iTTE1MTYuMTcsMTMxNy42NGgtMTEuNjN2LTQ2LjE4aDExLjYzdjQ2LjE4Wm0xLjI1LTU5LjY5Yy4zOCw4LjU3LTE0LjcyLDguNTUtMTQuMzMsMEMxNTAyLjY0LDEyNDkuMjgsMTUxNy44NiwxMjQ5LjI2LDE1MTcuNDIsMTI1OFoiIHRyYW5zZm9ybT0idHJhbnNsYXRlKC02Mi45MiAtODMzLjc3KSIvPjxwYXRoIGNsYXNzPSJjbHMtMiIgZD0iTTE1NjIuMTMsMTI5NC41YzItMTkuMzUtMjYuNDQtMTkuMzUtMjQuNDMsMC0yLDE5LjU2LDI2LjQ4LDE5LjU2LDI0LjQzLDBabTExLjkyLDBjMi40MSwzMS43Ny01MC41OCwzMS43MS00OC4yNywwQzE1MjMuNTQsMTI2MywxNTc2LjMzLDEyNjMsMTU3NC4wNSwxMjk0LjVaIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSgtNjIuOTIgLTgzMy43NykiLz48cGF0aCBjbGFzcz0iY2xzLTIiIGQ9Ik0xNjQwLjI5LDEyOTQuODZjMi4zMi0yNC4zLTM0LjQzLTI0LjI5LTMyLjExLDAtMi4zMiwyNC4yLDM0LjQ0LDI0LjIsMzIuMTEsMFptNi42MywwYzIuMzgsMzAuODktNDcuNzYsMzAuODktNDUuMzgsMEMxNjAwLDEyNjMuNDksMTY0OC40NywxMjYzLjUsMTY0Ni45MiwxMjk0Ljg2WiIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoLTYyLjkyIC04MzMuNzcpIi8+PHBhdGggY2xhc3M9ImNscy0yIiBkPSJNMTY1OC4wNywxMjczLjU4YzQzLTExLjgzLDM5LjU0LDE0LjgxLDM4Ljc1LDQ0LjA2aC02LjI1Yy0uNjgtMTkuMTQsNy00OS41Ni0yNi4yNS00MC4wOXY0MC4wOWgtNi4yNVoiIHRyYW5zZm9ybT0idHJhbnNsYXRlKC02Mi45MiAtODMzLjc3KSIvPjxwYXRoIGNsYXNzPSJjbHMtMiIgZD0iTTE3NDMuNjQsMTI4MC4yOWMtMzguMjUtMjEuMS00MC40Nyw0NiwwLDMydi0zMlptNi4yNSwzNS45M2MtNTIuNzUsMjEuMDctNTcuODctNjItNi4yNS00MS40OXYtMjQuNTVsNi4yNS0xLjA2WiIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoLTYyLjkyIC04MzMuNzcpIi8+PHBhdGggY2xhc3M9ImNscy0yIiBkPSJNMTc5Ni4zMywxMjkxLjE1YzEuNTYtMjAuNTQtMjguNzItMTguNjUtMjguNDYsMFptLTM1LjI5LDMuNjJjLTEuNjctMzEuMjEsNDYuOTItMzEuOTEsNDEuNTQsMS4yM2gtMzQuOWMtMS4yNSwxNi4yNiwxNy4xNCwyMSwzMC4zOCwxNWwxLjE2LDQuOTRDMTc4MiwxMzI0LDE3NTguNjksMTMxNC4zNiwxNzYxLDEyOTQuNzdaIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSgtNjIuOTIgLTgzMy43NykiLz48cGF0aCBjbGFzcz0iY2xzLTIiIGQ9Ik0xODY2LjEzLDEyOTEuMTVjMS41Ni0yMC41NC0yOC43Mi0xOC42NS0yOC40NiwwWm0tMzUuMjksMy42MmMtMS42Ny0zMS4yMSw0Ni45Mi0zMS45MSw0MS41MywxLjIzaC0zNC44OWMtMS4yNSwxNi4yNiwxNy4xNCwyMSwzMC4zOCwxNUwxODY5LDEzMTZDMTg1MS44MSwxMzI0LDE4MjguNDksMTMxNC4zNiwxODMwLjg0LDEyOTQuNzdaIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSgtNjIuOTIgLTgzMy43NykiLz48cGF0aCBjbGFzcz0iY2xzLTIiIGQ9Ik0xODk0LjMsMTMxMy41OGM4LjczLDEuMTEsMTcuMDgtNi4yNSw5LjcxLTEyLjcyLTYuNTYtNC41Mi0xOC4wNi01LjIyLTIyLjQtMTIuNTQtNy4xNy0xNS45LDE3LjItMjAuOTMsMjktMTUuMThsLTEuNDQsNS4xMmMtNi40Mi0zLjc4LTI2LjYxLTQtMjEuNjMsNy43OSw0LjYzLDYuMDYsMTUuMTMsNi4xMywyMS4wNiwxMSwxNywxNy4xLTE0LjkxLDI3LjE2LTI5LDE5LjExbDEuNjQtNS4yQTI5LjY1LDI5LjY1LDAsMCwwLDE4OTQuMywxMzEzLjU4WiIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoLTYyLjkyIC04MzMuNzcpIi8+PHBhdGggY2xhc3M9ImNscy0yIiBkPSJNMTkyOC43MSwxMjcyaDE5LjgxdjQuODZoLTE5LjgxYy44NSwxMy40OS02LjQ4LDQ3LDIwLjM5LDM0LjE3bDEuNTQsNC43NmMtNDAuNjQsMTcuMzktMjUuNDctMzguNzUtMjguMTgtNTcuMjFsNi4yNS0xLjA2WiIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoLTYyLjkyIC04MzMuNzcpIi8+PHBhdGggY2xhc3M9ImNscy0yIiBkPSJNMTk2My45LDEzMTcuNjRoLTYuMjRWMTI3Mmg2LjI0djQ1LjY1Wm0xLjQ1LTU5LjQzYy4zMiw1Ljc4LTkuNTYsNS43OC05LjIzLDBDMTk1NS43OSwxMjUyLjQ0LDE5NjUuNjcsMTI1Mi40NCwxOTY1LjM1LDEyNTguMjFaIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSgtNjIuOTIgLTgzMy43NykiLz48cGF0aCBjbGFzcz0iY2xzLTIiIGQ9Ik0xOTkzLjA5LDEzMTEuNDZhMTk1LjM0LDE5NS4zNCwwLDAsMCwxNC43NS0zOS40N2g2LjI1YTI0MiwyNDIsMCwwLDEtMTguMjgsNDUuNjVIMTk5MGEyMzguNTEsMjM4LjUxLDAsMCwxLTE4LjI5LTQ1LjY1aDYuNjNBMTk1LjQxLDE5NS40MSwwLDAsMCwxOTkzLjA5LDEzMTEuNDZaIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSgtNjIuOTIgLTgzMy43NykiLz48cGF0aCBjbGFzcz0iY2xzLTIiIGQ9Ik0yMDU0LjA5LDEyOTEuMTVjMS41Ni0yMC41NC0yOC43Mi0xOC42NS0yOC40NiwwWm0tMzUuMjksMy42MmMtMS42Ny0zMS4yMSw0Ni45Mi0zMS45MSw0MS41NCwxLjIzaC0zNC45Yy0xLjI1LDE2LjI2LDE3LjE0LDIxLDMwLjM4LDE1TDIwNTcsMTMxNkMyMDM5Ljc3LDEzMjQsMjAxNi40NSwxMzE0LjM2LDIwMTguOCwxMjk0Ljc3WiIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoLTYyLjkyIC04MzMuNzcpIi8+PC9zdmc+" alt="Portador Diário">
            </div>
            <div class="header-info">
                <strong>Portador Diário, Lda</strong><br>
                Maputo - Sede: Av. Acordos de Lusaka, nº 3237<br>
                Móvel: 82/84 1208151; 862051706<br>
                Email: info@portadordiario.co.mz &nbsp;|&nbsp; Web: www.portadordiario.co.mz
            </div>
        </div>

        <div class="header-divider"></div>

        @yield('content')

    </div>
</body>

</html>