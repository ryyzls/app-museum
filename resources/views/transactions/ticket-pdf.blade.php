<!DOCTYPE html>
<html>

<head>

    <meta charset="UTF-8">

    <title>ALPHASEUM E-Ticket</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 40px;
            background: #111111;
            font-family: DejaVu Sans, sans-serif;
            color: #ffffff;
        }

        .container {
            width: 100%;
        }

        /* Hero */
        .hero {
            text-align: center;
            margin-bottom: 40px;
        }

        .hero-subtitle {
            font-size: 11px;
            letter-spacing: 5px;
            text-transform: uppercase;
            color: #9ca3af;
            margin-bottom: 14px;
        }

        .hero-title {
            font-size: 44px;
            font-weight: 300;
            margin-bottom: 12px;
        }

        .hero-description {
            font-size: 14px;
            color: #d1d5db;
        }

        /* Main Card */
        .card {
            background: #1a1a1a;
            border: 1px solid #2d2d2d;
            border-radius: 28px;
            padding: 30px;
        }

        .layout {
            width: 100%;
        }

        .left-column {
            width: 42%;
            vertical-align: top;
            padding-right: 24px;
        }

        .right-column {
            width: 58%;
            vertical-align: top;
        }

        /* Exhibition */
        .exhibition-image {
            width: 100%;
            height: 240px;
            border-radius: 18px;
            margin-bottom: 22px;
        }

        .exhibition-title {
            font-size: 32px;
            line-height: 1.2;
            margin-bottom: 10px;
            font-weight: 300;
        }

        .exhibition-subtitle {
            font-size: 14px;
            line-height: 1.7;
            color: #cbd5e1;
        }

        /* Detail */
        .detail-row {
            margin-bottom: 22px;
        }

        .label {
            font-size: 10px;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: #9ca3af;
            margin-bottom: 6px;
        }

        .value {
            font-size: 18px;
            color: #ffffff;
        }

        /* QR */
        .qr-wrapper {
            text-align: center;
            margin-top: 35px;
            margin-bottom: 35px;
            padding-top: 24px;
            border-top: 1px solid #2d2d2d;
        }

        .qr-label {
            font-size: 10px;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: #9ca3af;
            margin-bottom: 14px;
        }

        /* Total */
        .total-section {
            border-top: 1px solid #2d2d2d;
            padding-top: 26px;
        }

        .total-label {
            font-size: 10px;
            letter-spacing: 4px;
            text-transform: uppercase;
            color: #9ca3af;
            margin-bottom: 12px;
        }

        .total-price {
            font-size: 64px;
            font-weight: 300;
            line-height: 1;
        }

        /* Footer */
        .footer {
            margin-top: 28px;
            text-align: center;
            color: #6b7280;
            font-size: 10px;
            letter-spacing: 2px;
        }
    </style>

</head>

<body>

    <div class="container">

        {{-- Hero --}}
        <div class="hero">

            <div class="hero-subtitle">

                ALPHASEUM DIGITAL ACCESS

            </div>

            <div class="hero-title">

                Museum E-Ticket

            </div>

            <div class="hero-description">

                Reservation confirmation for your museum experience.

            </div>

        </div>

        {{-- Card --}}
        <div class="card">

            <table class="layout">

                <tr>

                    {{-- LEFT --}}
                    <td class="left-column">

                        <img src="{{ $transaction->ticket->exhibition->banner_image }}" class="exhibition-image">

                        <div class="exhibition-title">

                            {{ $transaction->ticket->exhibition->title }}

                        </div>

                        <div class="exhibition-subtitle">

                            {{ $transaction->ticket->exhibition->subtitle }}

                        </div>

                    </td>

                    {{-- RIGHT --}}
                    <td class="right-column">

                        <div class="detail-row">

                            <div class="label">

                                Transaction Code

                            </div>

                            <div class="value">

                                {{ $transaction->transaction_code }}

                            </div>

                        </div>

                        <div class="detail-row">

                            <div class="label">

                                Ticket Type

                            </div>

                            <div class="value">

                                {{ $transaction->ticket->ticket_type }}

                            </div>

                        </div>

                        <div class="detail-row">

                            <div class="label">

                                Visit Date

                            </div>

                            <div class="value">

                                {{ $transaction->ticket->visit_date->format('d M Y') }}

                            </div>

                        </div>

                        <div class="detail-row">

                            <div class="label">

                                Quantity

                            </div>

                            <div class="value">

                                {{ $transaction->quantity }}

                            </div>

                        </div>

                        {{-- QR --}}
                        <div class="qr-wrapper">

                            <div class="qr-label">

                                Digital Reservation Validation

                            </div>

                            <img src="data:image/svg+xml;base64,{{ $qrCode }}" width="130">

                        </div>

                        {{-- Total --}}
                        <div class="total-section">

                            <div class="total-label">

                                Total Payment

                            </div>

                            <div class="total-price">

                                €{{ number_format($transaction->total_price, 0) }}

                            </div>

                        </div>

                    </td>

                </tr>

            </table>

        </div>

        {{-- Footer --}}
        <div class="footer">

            ALPHASEUM • DIGITAL MUSEUM RESERVATION SYSTEM

        </div>

    </div>

</body>

</html>