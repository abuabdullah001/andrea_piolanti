<!DOCTYPE html>
<html lang="en" style="margin: 0; padding: 0;">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Invoice Email</title>

    <style>
        a {
            text-decoration: none;
            color: #278efc;
        }
    </style>
</head>

<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <table id="invoice" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f4f4f4; padding: 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" border="0"
                    style="background-color: #ffffff; border-radius: 6px; overflow: hidden;">
                    <!-- Logo -->
                    <tr>
                        <td align="center" style="padding: 30px 0;">
                            <img src="{{ $invoice['owner']['avatar'] }}" alt="Anulysse Branding Logo"
                                width="180" />
                        </td>
                    </tr>
                    <!-- Title -->
                    <tr>
                        <td align="center" style="padding: 0 30px;">
                            <h2 style="margin: 0; font-size: 20px; color: #333;">{{ $invoice['owner']['name'] }}</h2>
                            <p style="margin: 5px 0 20px; color: #888;">New Invoice</p>
                            <h1 style="color: #333; font-size: 36px; margin: 0;">${{ $invoice['booking']['due']  }}</h1>
                            <p style="color: #666;">Due on {{ $invoice['booking']['created_at']->format('F j, Y')  }}</p>
                        </td>
                    </tr>
                    <!-- Pay Button -->
                    <tr>
                        <td align="center" style="padding: 20px 30px;">
                            <a href="#"
                                style="background-color: #f2c94c;
                                color: #000;
                                text-decoration: none;
                                padding: 20px 25px;
                                border-radius: 4px;
                                font-weight: bold;
                                display: block;">Pay
                                Invoice</a>
                        </td>
                    </tr>
                    <!-- Invoice Details -->
                    <tr>
                        <td style="padding: 0 30px 20px;">
                            <hr style="border: none; border-top: 1px solid #eee;" />
                            <p>
                                <strong>{{ $invoice['service']['title'] }}</strong>
                                <br>
                                <br>
                                Invoice #{{ $invoice['booking']['invoice_no'] }}
                                <br>
                                <br>
                                {{ $invoice['booking']['created_at']->format('F j, Y')  }}
                            </p>

                            <p>
                                <strong>Customer</strong>
                                <br>
                                <br>
                                {{ $invoice['customer']['name'] }}
                                <br>
                                <a href="mailto:anulysseestar@gmail.com">{{ $invoice['customer']['email'] }}</a><br>
                                {{ $invoice['customer']['phone'] }}
                            </p>

                            <p>
                                <strong>Date of service</strong>
                                <br>
                                <br>
                                {{ $invoice['booking']['date']  }}
                            </p>

                            <p><a href="javascript:void(0)" onclick="downloadPDF()" style="color: #007bff;">Download Invoice PDF</a></p>
                        </td>
                    </tr>
                    <!-- Invoice Summary -->
                    <tr>
                        <td style="padding: 0 30px 30px;">
                            <table width="100%" cellpadding="10" cellspacing="0" border="0"
                                style="border: 1px solid #ddd; border-radius: 6px;">
                                <tr>
                                    <td><strong>Invoice summary</strong></td>
                                    <td></td>
                                </tr>
                                @foreach ($invoice['items'] as $item)
                                <tr>
                                    <td>{{ $item->description }}</td>
                                    <td align="right">${{ $item->price }}</td>
                                </tr>
                                @endforeach
                                <tr>
                                    <td><strong>Subtotal</strong></td>
                                    <td align="right">${{ $invoice['booking']['subtotal'] }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Total Due</strong></td>
                                    <td align="right"><strong>${{ $invoice['booking']['total'] }}</strong></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <!-- Footer -->
                    <!-- <tr>
                        <td align="center" style="padding: 20px 30px; font-size: 12px; color: #aaa;">
                            © 2025 Anulysse Branding. All rights reserved.
                        </td>
                    </tr> -->
                </table>
            </td>
        </tr>
    </table>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <script>
        function downloadPDF() {
            const element = document.getElementById('invoice');

            const opt = {
                margin: 0.5,
                filename: 'invoice.pdf',
                image: {
                    type: 'jpeg',
                    quality: 0.98
                },
                html2canvas: {
                    scale: 2
                },
                jsPDF: {
                    unit: 'in',
                    format: 'letter',
                    orientation: 'portrait'
                }
            };

            html2pdf().set(opt).from(element).save();
        }
    </script>

</body>

</html>
