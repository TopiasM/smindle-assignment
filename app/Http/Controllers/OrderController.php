<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderContent;
use App\Enums\ContentKind;
use Illuminate\Http\Request;
use App\Jobs\ProcessRecurringItem;

class OrderController extends Controller
{
    var $validationArray = [
        'client.identity' => 'required|string|max:255',
        'client.contact_point' => 'required|string|max:255',
        'contents' => 'required|array',
        'contents.*.label' => 'required|string|max:255',
        'contents.*.kind' => 'required|in:single,recurring',
        'contents.*.cost' => 'required|numeric|min:0',
        'contents.*.meta.*' => 'nullable|string',
    ];

    /**
     * Create a new order.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        try {
            $requestData = $request->validate($this->validationArray);

            $order = $this->storeOrder($requestData);

            return response()->json([
                'status' => 1,
                'message' => 'Order created successfully',
                'order' => $order,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['status' => 0, 'message' => $e->getMessage()], 422);
        }
    }

    /**
     * Store a new order in the database.
     *
     * @param array $data
     * @return Order
     */
    private function storeOrder(array $data): Order {
        $orderId = Order::max('id') + 1;

        $order = new Order();
        $order->client_identity = $data['client']['identity'];
        $contactPoint = explode(', ', $data['client']['contact_point']);
        $order->client_address = implode(', ', array_slice($contactPoint, 0, -1)); // All but last part as address
        $country = end($contactPoint);
        $order->client_country = $country;
        $order->order_number = "{$country}{$orderId}"; // Unique order number based on country and max ID
        $order->save();

        foreach ($data['contents'] as $content) {
            $kind = ContentKind::from($content['kind']);
            $orderContent = new OrderContent();
            $orderContent->order_number = $order->order_number;
            $orderContent->label = $content['label'];
            $orderContent->kind = $kind;
            $orderContent->cost = $content['cost'];
            $orderContent->metadata = json_encode($content['meta'] ?? []);
            $orderContent->order()->associate($order);
            $orderContent->save();

            if($kind == ContentKind::Recurring) {
                $frequency = $content['meta']['frequency'] ?? env('DEFAULT_ITEM_FREQUENCY', 'monthly');
                /* 
                    Not doing anything spesific with frequency. 
                    But if frequency was unspecified, an email could be sent to the client
                */

                // Dispatch job for recurring items
                $priority = $content['meta']['priority']  ?? 'default';
                $item = $content['label'];
                $value = $content['cost'];
                $moment = now()->toDateTimeString();
                ProcessRecurringItem::dispatch($item, $value, $moment)->onQueue($priority);
                \Log::info("Dispatched job for recurring item({item})", ['item' => $item]);
            }
        }

        return $order->load('orderContents');
    }
}
