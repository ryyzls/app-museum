<?php

namespace App\Http\Controllers;

use App\Jobs\SendOrderEmail;
use App\Models\Order;

class OrderController
{
    public function index()
    {
        return Order::all();
    }

    public function store()
    {
        $order = Order::create(['status' => 'pending']);
        SendOrderEmail::dispatch($order);

        return $order;
    }

    public function show($id)
    {
        return Order::findOrFail($id);
    }

    public function destroy($id)
    {
        Order::findOrFail($id)->delete();

        return response()->noContent();
    }
}
