<?php

namespace App\Http\Controllers;

use App\Models\LabTest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Patient;
use App\Notifications\LabOrderCreated;
use App\Notifications\LabOrderStatusUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class LabController extends Controller
{
    public function index(Request $request): Response
    {
        $orders = Order::with(['patient', 'items.test'])->latest()->get();

        return Inertia::render('Lab', [
            'patients' => Patient::withCount('orders')->with(['orders' => fn ($query) => $query->with('items.test')->latest()])->latest()->get(), 'tests' => LabTest::orderBy('category')->get(), 'orders' => $orders,
            'stats' => ['today_orders' => Order::whereDate('created_at', today())->count(), 'pending' => Order::where('status', '!=', 'completed')->count(), 'patients' => Patient::count(), 'revenue' => Order::whereDate('created_at', today())->sum('paid'), 'outstanding' => Order::selectRaw('sum(total - discount - paid) as balance')->value('balance') ?? 0],
            'monthly' => Order::selectRaw("strftime('%m', created_at) month, sum(paid) total")->groupBy('month')->pluck('total', 'month'),
            'flash' => session('success'),
            'notifications' => $request->user()->notifications()->latest()->limit(12)->get(),
            'unreadNotifications' => $request->user()->unreadNotifications()->count(),
            'receiptOrderId' => session('receipt_order_id'),
        ]);
    }

    public function printReceipt(Order $order): Response
    {
        return Inertia::render('Print/Receipt', [
            'order' => $order->load(['patient', 'items.test']),
        ]);
    }

    public function printReport(Order $order): Response
    {
        return Inertia::render('Print/Report', [
            'order' => $order->load(['patient', 'items.test']),
        ]);
    }

    public function storePatient(Request $request)
    {
        $data = $request->validate(['name' => 'required|max:100', 'phone' => 'required|max:20', 'cnic' => 'nullable|max:20', 'gender' => 'required|in:Male,Female,Other', 'age' => 'required|integer|min:0|max:120', 'city' => 'required|max:80', 'referred_by' => 'nullable|max:100']);
        $data['patient_no'] = 'MLT-'.str_pad((string) (Patient::max('id') + 1), 5, '0', STR_PAD_LEFT);
        Patient::create($data);

        return back()->with('success', 'Patient registered successfully.');
    }

    public function updatePatient(Request $request, Patient $patient)
    {
        $patient->update($request->validate(['name' => 'required|max:100', 'phone' => 'required|max:20', 'cnic' => 'nullable|max:20', 'gender' => 'required|in:Male,Female,Other', 'age' => 'required|integer|min:0|max:120', 'city' => 'required|max:80', 'referred_by' => 'nullable|max:100']));

        return back()->with('success', 'Patient updated.');
    }

    public function destroyPatient(Patient $patient)
    {
        $patient->delete();

        return back()->with('success', 'Patient removed.');
    }

    public function storeTest(Request $request)
    {
        LabTest::create($request->validate(['code' => 'required|unique:lab_tests|max:20', 'name' => 'required|max:120', 'category' => 'required|max:60', 'price' => 'required|integer|min:0', 'sample_type' => 'required|max:60', 'turnaround' => 'required|max:60']));

        return back()->with('success', 'Test added to catalogue.');
    }

    public function storeOrder(Request $request)
    {
        $data = $request->validate(['patient_id' => 'required|exists:patients,id', 'test_ids' => 'required|array|min:1', 'test_ids.*' => 'exists:lab_tests,id', 'discount' => 'nullable|integer|min:0', 'paid' => 'nullable|integer|min:0', 'payment_method' => 'required']);
        $order = DB::transaction(function () use ($data) {
            $tests = LabTest::whereIn('id', $data['test_ids'])->get();
            $total = $tests->sum('price');
            $order = Order::create(['invoice_no' => 'INV-'.now()->format('ymd').'-'.str_pad((string) (Order::max('id') + 1), 4, '0', STR_PAD_LEFT), 'patient_id' => $data['patient_id'], 'total' => $total, 'discount' => $data['discount'] ?? 0, 'paid' => $data['paid'] ?? 0, 'payment_method' => $data['payment_method']]);
            foreach ($tests as $test) {
                OrderItem::create(['order_id' => $order->id, 'lab_test_id' => $test->id, 'price' => $test->price]);
            }

            return $order;
        });
        $request->user()->notify(new LabOrderCreated($order->load('patient')));

        return back()
            ->with('success', 'New lab order created.')
            ->with('receipt_order_id', $order->id);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $data = $request->validate(['status' => 'required|in:pending,sample_collected,processing,completed']);
        $order->update($data + ['reported_at' => $data['status'] === 'completed' ? now() : null]);
        $request->user()->notify(new LabOrderStatusUpdated($order));

        return back()->with('success', 'Order status updated.');
    }

    public function updateResults(Request $request, Order $order)
    {
        $data = $request->validate(['items' => 'required|array', 'items.*.id' => 'required|exists:order_items,id', 'items.*.result' => 'nullable|max:100', 'items.*.unit' => 'nullable|max:50', 'items.*.reference_range' => 'nullable|max:100', 'items.*.flag' => 'required|in:normal,high,low']);
        foreach ($data['items'] as $item) {
            $order->items()->whereKey($item['id'])->update($item);
        }
        $order->update(['status' => 'completed', 'reported_at' => now()]);
        $request->user()->notify(new LabOrderStatusUpdated($order));

        return back()->with('success', 'Results saved and report completed.');
    }
}
