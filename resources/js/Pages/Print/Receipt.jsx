import { Head } from '@inertiajs/react';
import { ArrowLeft, Printer } from 'lucide-react';
import { useState } from 'react';
import '../../../css/print.css';

const money = (amount) => `Rs. ${Number(amount || 0).toLocaleString('en-PK')}`;

export default function Receipt({ order }) {
    const [paper, setPaper] = useState(new URLSearchParams(window.location.search).get('paper') === '58' ? '58' : '80');
    const balance = Math.max(0, order.total - order.discount - order.paid);

    return <div className={`print-screen receipt-paper paper-${paper}`}>
        <Head title={`${order.invoice_no} Receipt`} />
        <div className="print-toolbar no-print">
            <button onClick={() => window.close()}><ArrowLeft size={17} /> Back</button>
            <div className="paper-options"><span>Thermal paper:</span><button className={paper === '58' ? 'selected' : ''} onClick={() => setPaper('58')}>58mm</button><button className={paper === '80' ? 'selected' : ''} onClick={() => setPaper('80')}>80mm</button></div>
            <button className="print-primary" onClick={() => window.print()}><Printer size={17} /> Select printer & print</button>
        </div>
        <article className="thermal-receipt">
            <header><img className="receipt-logo" src="/images/medilab-logo.png" alt="MediLab Diagnostics logo"/><h1>MEDILAB DIAGNOSTICS</h1><p>Bosan Road, Multan</p><p>061-4578901 · 0300-1234567</p></header>
            <div className="receipt-rule" />
            <dl className="receipt-info"><div><dt>Receipt</dt><dd>{order.invoice_no}</dd></div><div><dt>Date</dt><dd>{new Date(order.created_at).toLocaleString('en-PK')}</dd></div><div><dt>Patient</dt><dd>{order.patient.name}</dd></div><div><dt>Patient ID</dt><dd>{order.patient.patient_no}</dd></div><div><dt>Mobile</dt><dd>{order.patient.phone}</dd></div></dl>
            <div className="receipt-rule" />
            <table><thead><tr><th>Test</th><th>Amount</th></tr></thead><tbody>{order.items.map((item) => <tr key={item.id}><td>{item.test.name}<small>{item.test.code}</small></td><td>{money(item.price)}</td></tr>)}</tbody></table>
            <div className="receipt-rule" />
            <dl className="receipt-totals"><div><dt>Subtotal</dt><dd>{money(order.total)}</dd></div><div><dt>Discount</dt><dd>{money(order.discount)}</dd></div><div><dt>Paid ({order.payment_method})</dt><dd>{money(order.paid)}</dd></div><div className="grand"><dt>Balance</dt><dd>{money(balance)}</dd></div></dl>
            <div className="receipt-rule" /><footer><p>Thank you for choosing MediLab</p><small>Please bring this receipt when collecting your report.</small><b>{order.invoice_no}</b></footer>
        </article>
    </div>;
}
