import { Head } from '@inertiajs/react';
import { ArrowLeft, Printer } from 'lucide-react';
import '../../../css/print.css';

export default function Report({ order }) {
    return <div className="print-screen report-paper">
        <Head title={`${order.invoice_no} Laboratory Report`} />
        <div className="print-toolbar no-print"><button onClick={() => window.close()}><ArrowLeft size={17} /> Back</button><span>A4 Laboratory Report</span><button className="print-primary" onClick={() => window.print()}><Printer size={17} /> Select printer & print</button></div>
        <article className="a4-report">
            <header className="report-header"><div className="report-logo"><img src="/images/medilab-logo.png" alt="MediLab Diagnostics logo"/><div><h1>MediLab Diagnostics</h1><p>Accurate results. Trusted care.</p></div></div><div><b>Bosan Road, Multan</b><span>061-4578901 · 0300-1234567</span><span>info@medilab.pk</span></div></header>
            <div className="report-title"><h2>LABORATORY INVESTIGATION REPORT</h2><span>{order.invoice_no}</span></div>
            <section className="patient-strip"><div><span>Patient Name</span><b>{order.patient.name}</b></div><div><span>Patient ID</span><b>{order.patient.patient_no}</b></div><div><span>Age / Gender</span><b>{order.patient.age} yrs / {order.patient.gender}</b></div><div><span>Contact</span><b>{order.patient.phone}</b></div><div><span>Referred By</span><b>{order.patient.referred_by || 'Walk-in'}</b></div><div><span>Reported On</span><b>{new Date(order.reported_at || order.updated_at).toLocaleString('en-PK')}</b></div></section>
            <section className="result-section"><h3>TEST RESULTS</h3><table><thead><tr><th>Investigation</th><th>Result</th><th>Unit</th><th>Reference Range</th><th>Flag</th></tr></thead><tbody>{order.items.map((item) => <tr key={item.id}><td><b>{item.test.name}</b><small>{item.test.code} · {item.test.category}</small></td><td className={item.flag !== 'normal' ? 'abnormal' : ''}>{item.result || 'Pending'}</td><td>{item.unit || '—'}</td><td>{item.reference_range || 'As per age / gender'}</td><td><span className={`result-flag ${item.flag}`}>{item.flag || 'normal'}</span></td></tr>)}</tbody></table></section>
            <section className="report-note"><b>Important note</b><p>Laboratory results must be correlated with clinical findings. Reference ranges may vary according to age, gender and methodology.</p></section>
            <section className="signatures"><div><span>Lab Technologist</span><b>Verified electronically</b></div><div><span>Consultant Pathologist</span><b>Digital signature</b></div></section>
            <footer><span>Computer generated report — no manual alteration permitted.</span><b>Page 1 of 1</b></footer>
        </article>
    </div>;
}
