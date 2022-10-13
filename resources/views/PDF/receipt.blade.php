<!doctype html>
<html lang="eng">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
</head>
<body>
<table style="width: 100%">
    <tbody>
        <tr>
            <td>
                <table style="font-family:'Helvetica Neue',Helvetica,Arial,sans-serif; width: 100%; margin: 0 auto">
                    <tbody style="line-height: 1.5">
                        <tr>
                            <td style="font-size: 15px; font-weight: 400; font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;">
                                <div>
                                    <p>
                                        <div style="background-color: #F4F4F7; padding: 30px 0">
                                        <div style=" text-align: center; font-size: 22px"><strong>{{ $store->name }}</strong></div>
                                            <div style=" text-align: center">{{ $store->address }}</div>
                                            <div style=" text-align: center">Phone: {{ $store->phone_1 }}, {{ $store->phone_2 }}</div>
                                        </div>
                                    </p>
                                    <p>
                                        <div style="font-size: 22px;"><span style="color: #435ADE;">Receipt: </span><span style="color: #7A7D84;">#{{ $sale->receipt }}</span></div>
                                        <div><label><strong>Date: </strong></label><span>{{ $sale->created_at }}</span></div>
                                        <div><label><strong>Sold by: </strong></label><span>{{ $sale->added_by }}</span></div>
                                        <div><label><strong>Amount paid: </strong></label><span>{{ $store->currency_code }}</span><span>{{ $sale->amount_recieved }}</span></div>
                                        <div><label><strong>Change: </strong></label><span>{{ $store->currency_code }}</span><span>{{ $sale->balance }}</span></div>
                                    </p>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <table style="width: 100%; margin: 0 auto; font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;border-collapse: collapse;">
                                    <thead style="border-bottom: 2px solid #e9ebf0;">
                                        <tr style="text-align: left">
                                            <th style="padding: 12px 0;text-align:left">Product Description</th>
                                            <th style="padding: 12px 0; text-align:center">Qty</th>
                                            <th style="padding: 12px 0; text-align:center">Price <span>({{ $store->currency_code }})</span></th>
                                            <th style="padding: 12px 0;text-align: right;">Amount <span>({{ $store->currency_code }})</span></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($sale_items as $key => $value)
                                            <tr>
                                                <td style="padding: 8px 0;">{{ $value->product_name }}</td>
                                                <td style="padding: 8px 0; text-align:center"><span style="color:#7A7D84; margin-right: 6px">x</span>{{ $value->quantity }}</td>
                                                <td style="padding: 8px 0;text-align:center">{{ number_format(round($value->price_before, 2)) }}</td>
                                                <td style="padding: 8px 0;text-align: right;">{{ number_format(round($value->total_paid, 2)) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td>
                            <hr style="border-top: 2px solid #e9ebf0; border-bottom: none; border-right: none; border-left: none">
                                <table style="width: 50%; margin-left: auto;border-collapse: collapse;">
                                    <tbody>
                                        <thead>
                                            <tr>
                                                <th style="padding: 12px 0; text-align: left">Sub total:</th>
                                                <th style="text-align: right;padding: 12px 0">{{ number_format(round($sale->total_paid, 2)) }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td style="text-align: left; padding: 8px 0;color:#7A7D84;">Discount:</td>
                                                <td style="text-align: right; padding: 8px 0;color:#7A7D84;">0.00</td>
                                            </tr>
                                            <tr>
                                                <td style="text-align: left; padding: 8px 0;color:#7A7D84;">VAT(2%):</td>
                                                <td style="text-align: right; padding: 8px 0;color:#7A7D84;">2.00</td>
                                            </tr>
                                        </tbody>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <table style="width: 50%; margin-left: auto;border-collapse: collapse;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;">
                                    <thead>
                                        <th style="padding: 12px 0; text-align: left; font-size: 24px">Total Amount:</th>
                                        <th style="padding: 12px 0; text-align: right; font-size: 24px"><span style="font-weight: 400;margin-right: 6px">{{ $store->currency_code }}</span><span>{{ number_format(round($sale->total_paid, 2)) }}</span></th>
                                    </thead>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding-top: 100px">
                                <div style="font-family:'Helvetica Neue',Helvetica,Arial,sans-serif; padding: 20px 40px 0 40px;text-align: center">
                                    <div style="font-size: 12px; color: #7A7D84">
                                        <span>Powered by: Flexsale Inc.</span><br />
                                        <span>For more enquiries, contact us at <a style="color: #212121;" href="mailTo:info@flexsale.store">info@flexsale.store</a></span>
                                        <span> or visit our website <a style="color: #212121;" href="https://www.flexsale.store" target="_blank">www.flexsale.store</a> for more information.</span>
                                        <br>
                                        <div>© 2022 Flexsale Inc. All Rights Reserved.</div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>
</body>
</html>
