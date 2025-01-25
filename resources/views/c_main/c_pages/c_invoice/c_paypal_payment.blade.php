@extends('c_main.c_dashboard')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="invoice-container">
        <div class="invoice-header">
            <h4>Pay Invoice: {{ $invoice->invoice_no }}</h4>
        </div>

        <table class="table table-borderless">
            <thead>
                <tr>
                    <th class="text-start">Item</th>
                    <th class="text-start">Price</th>
                    <th class="text-start">Quantity</th>
                    <th class="text-end">Item Total ({{ $invoice->currency }})</th>
                    <th class="text-end">Item Total (CAD)</th>
                </tr>
            </thead>
            <tbody>
                @php 
                    $next_payment_recurring = 0; 
                    $next_payment_recurring_discount = 0; 
                    $total_discount = 0;
                    $interval_total = [];
                    $interval = '';
                    $interval_text = '';
                    $trial_amount = 0;  
                    $total = 0;
                    $first_type = '';
                    $first_type_interval = 1;
                @endphp

                @foreach($invoice->items as $key=>$item)
                <tr>
                    <td class="text-start">
                        {{ $item->service->service_name ?? $item->item_name }}<br>
                        @php $service = $item->service @endphp
                        @if(!empty($item->service->trial_for))
                            ${{$service->trial_price - $item->discount}} for {{$service->trial_for}} {{ $service->trial_for > 1 ? $service->trial_period . 's' : $service->trial_period }}, then
                            ${{ $item->service->recurring_service_currency_value - $item->discountsnextpayment}}/{{ $service->recurring_service_currency_value_two }} 
                            {{ $service->recurring_service_currency_value_two > 1 ? $service->recurring_service_currency_value_two_type . 's' : $service->recurring_service_currency_value_two_type }}
                            @php 
                                $next_payment_recurring += ($item->service->recurring_service_currency_value * $item->quantity) - $item->discountsnextpayment * $item->quantity; 
                                $interval_total[] = $item->service->trial_for;
                                $trial_amount += $service->trial_price - $item->discount;
                                $total += ($item->price * $item->quantity - $item->discount * $item->quantity);
                                $total_discount += $item->discount * $item->quantity;
                                $next_payment_recurring_discount += $item->discountsnextpayment * $item->quantity;
                                
                                if($key==0){
                                    $first_type_interval = $item->service->recurring_service_currency_value_two;
                                }
                            @endphp

                        @elseif($item->service->service_type=='recurring')
                                ${{ $item->service->recurring_service_currency_value - $item->discount}}/{{ $item->service->recurring_service_currency_value_two }} 
                                {{ $service->recurring_service_currency_value_two > 1 ? $service->recurring_service_currency_value_two_type . 's' : $service->recurring_service_currency_value_two_type }}
                                @php 
                                    $next_payment_recurring += ($item->service->recurring_service_currency_value * $item->quantity) - $item->discount * $item->quantity; 
                                    $total_discount += $item->discount * $item->quantity;
                                    $interval_total[] = $item->service->recurring_service_currency_value_two;
                                    $total += ($item->price * $item->quantity - $item->discount * $item->quantity);
                                    $next_payment_recurring_discount += $item->discountsnextpayment * $item->quantity; 

                                    if($trial_amount!=0){
                                        $trial_amount += ($item->service->recurring_service_currency_value * $item->quantity) - $item->discountsnextpayment;
                                    }

                                    if($key==0){
                                        $first_type_interval = $item->service->recurring_service_currency_value_two;
                                    }
                                @endphp
                        @else
                            @php $total += ($item->price * $item->quantity - $item->discount * $item->quantity); @endphp
                        @endif
                    </td>
                    
                    @if($item->service->service_type!="onetime")
                        @if($item->discount)
                            <td class="text-start"><del>{{ $invoice->currency }} {{ number_format($item->price, 2) }}</del><br>{{ $invoice->currency }} {{ number_format($item->price - $item->discount, 2) }}</td>
                        @else
                            <td class="text-start">{{ $invoice->currency }} {{ number_format($item->price, 2) }}</td>
                        @endif
                    @else
                        @if($item->discount)
                            <td class="text-start"><del>{{ $invoice->currency }} {{ number_format($item->price, 2) }}</del><br>{{ $invoice->currency }} {{ number_format($item->price - $item->discount, 2) }}</td>
                        @else
                            <td class="text-start">{{ $invoice->currency }} {{ number_format($item->price, 2) }}</td>
                        @endif
                    @endif


                    <td class="text-start">× {{ $item->quantity }}</td>
                    <td class="text-end">
                        @if($item->service->service_type!="onetime")
                            {{ $invoice->currency }} {{ number_format($item->price * $item->quantity - $item->discount * $item->quantity, 2) }}
                        @else
                            {{ $invoice->currency }} {{ number_format($item->price * $item->quantity - $item->discount, 2) }}
                        @endif
                    </td>
                    
                    <td class="text-end">
                        ${{ number_format($item->price * $item->quantity - $item->discount * $item->quantity, 2) }}
                    </td>
                </tr>
                @endforeach

                @if($invoice->upfront_payment_amount > 0)
                    <tr>
                        <td class="text-start"><strong>Upfront Payment</strong></td>
                        <td class="text-start">
                            -{{ $invoice->currency }} {{ number_format($invoice->upfront_payment_amount, 2) }}
                        </td>
                        <td class="text-start">×1</td>
                        <td class="text-end"> -{{ $invoice->currency }} {{ number_format($invoice->upfront_payment_amount, 2) }}</td>
                        <td class="text-end text-danger">
                            -${{ number_format($invoice->upfront_payment_amount, 2) }}
                        </td>
                    </tr>
                @endif
            </tbody>
            <tfoot>
                <!-- Subtotal -->
                <tr>
                    <td colspan="2"></td>
                    <td class="text-end"><strong>Subtotal</strong></td>

                    @if($item->service->service_type!="onetime")
                        <td class="text-end">
                            {{ $invoice->currency }} 
                            {{ number_format($total, 2) }}
                        </td>

                        <td class="text-end">
                            ${{ number_format($total, 2) }}
                        </td>
                    @else
                        <td class="text-end">
                            {{ $invoice->currency }} 
                            {{ number_format($total - $invoice->upfront_payment_amount, 2) }}
                        </td>

                        <td class="text-end">
                            ${{ number_format($total - $invoice->upfront_payment_amount, 2) }}
                        </td>
                    @endif
                </tr>

                <!-- Payment Due -->
                <tr>
                    <td colspan="2"></td>
                    <td class="text-end"><strong>Payment Due</strong></td>

                    @if($item->service->service_type!="onetime")
                        <td class="text-end">
                            <strong>{{ $invoice->currency }} {{ number_format($total, 2) }}</strong>
                        </td>
                        <td class="text-end">
                            <strong>CAD ${{ number_format($total, 2) }}</strong>
                        </td>
                    @else
                        <td class="text-end">
                            <strong>{{ $invoice->currency }} {{ number_format($total - $invoice->upfront_payment_amount, 2) }}</strong>
                        </td>
                        <td class="text-end">
                            <strong>CAD ${{ number_format($total - $invoice->upfront_payment_amount, 2) }}</strong>
                        </td>
                    @endif
                </tr>
            </tfoot>
        </table>

        <div class="invoice-card">
            <p><strong>Client:</strong> {{ $invoice->client->first_name }} {{ $invoice->client->last_name }}</p>
            @if($total_discount)
                <p><strong>Discount:</strong> -${{ number_format($total_discount,2) }}</p>
            @endif

            @if($next_payment_recurring)
                @php
                    $interval = ceil(array_sum($interval_total) / count($interval_total));
                    $interval_text = $interval == 1 ? 'month' : $interval . ' months';
                @endphp

                @if($trial_amount)
                    <p><strong>Total Amount:</strong> ${{ number_format($total, 2) }} now, then ${{ number_format($next_payment_recurring, 2) }}/{{$first_type_interval}} {{ strtolower($main_data['firstServiceType']) }}{{ $first_type_interval > 1 ? 's' : '' }}</p>
                @else
                    <p><strong>Total Amount:</strong>${{ number_format($next_payment_recurring, 2) }}/{{$first_type_interval}} {{ strtolower($main_data['firstServiceType']) }}{{ $first_type_interval > 1 ? 's' : '' }}</p>
                @endif
            @else
                <p><strong>Total Amount:</strong> ${{ number_format($total - $invoice->upfront_payment_amount, 2) }}</p>
            @endif
        </div>

        @if($next_payment_recurring)
            @if($trial_amount)                        
                <a class="btn btn-primary mt-4 w-100" id="recurring-submit-button" type="button" href="{{ route('portal.paypal.createSubscriptionPlan', $invoice->id) }}">
                    <i class="fas fa-lock"></i>&nbsp;&nbsp; 
                    Pay ${{ number_format($total, 2) }} now, then ${{ number_format($next_payment_recurring, 2) }}/{{$first_type_interval}} 
                    {{ strtolower($main_data['firstServiceType']) }}{{ $first_type_interval > 1 ? 's' : '' }}
                </a>
            @else
                <a class="btn btn-primary mt-4 w-100" id="recurring-submit-button" type="button" href="{{ route('portal.paypal.createSubscriptionPlan', $invoice->id) }}">
                    <i class="fas fa-lock"></i>
                    ${{ number_format($next_payment_recurring, 2) }}/{{$first_type_interval}} 
                    {{ strtolower($main_data['firstServiceType']) }}{{ $first_type_interval > 1 ? 's' : '' }}
                </a>
            @endif
        @else
            <a class="btn btn-primary mt-4 w-100" id="recurring-submit-button" type="button" href="{{route('portal.paypal.create.payment',$invoice->id)}}">
                <i class="fas fa-lock"></i> Pay ${{ number_format($total - $invoice->upfront_payment_amount, 2) }}
            </a>
        @endif
    </div>
</div>

<style>
    .invoice-container {
        background: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    .invoice-header {
        margin-bottom: 20px;
        text-align: center;
    }
    .invoice-card {
        margin-bottom: 30px;
    }
    #card-element {
        border: 1px solid #e0e0e0;
        padding: 10px;
        border-radius: 4px;
        background-color: #f8f9fa;
    }
    #submit-button {
        font-size: 16px;
        font-weight: bold;
    }
</style>
@endsection
