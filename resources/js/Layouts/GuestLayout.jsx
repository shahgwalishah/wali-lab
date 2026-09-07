import ApplicationLogo from '@/Components/ApplicationLogo';
import { Link } from '@inertiajs/react';

export default function GuestLayout({ children }) {
    return (
        <div className="min-h-screen bg-[#f1f6f5] p-4 sm:grid sm:place-items-center">
            <div className="grid w-full max-w-4xl overflow-hidden rounded-2xl bg-white shadow-[0_24px_80px_rgba(15,61,57,0.15)] lg:grid-cols-[1.05fr_1fr]">
                <div className="relative hidden overflow-hidden bg-[#0f3d39] p-10 text-white lg:flex lg:flex-col lg:justify-between">
                    <div className="absolute -right-24 -top-24 h-64 w-64 rounded-full bg-[#10a18f]/20" />
                    <Link href="/" className="relative flex items-center gap-3">
                        <span className="grid h-14 w-14 place-items-center rounded-xl bg-white p-2"><ApplicationLogo className="h-full w-full object-contain" /></span>
                        <span><b className="block font-[Manrope] text-xl">MediLab <i className="not-italic text-[#4ed0bd]">Pro</i></b><small className="text-xs text-[#a5cbc5]">Laboratory Management System</small></span>
                    </Link>
                    <div className="relative">
                        <p className="mb-3 text-xs font-semibold uppercase tracking-[0.2em] text-[#56cdbc]">Accurate. Secure. Connected.</p>
                        <h2 className="text-3xl font-bold leading-tight">Smarter diagnostics for better patient care.</h2>
                        <p className="mt-4 text-sm leading-6 text-[#afd1cc]">Patients, testing, reports, billing and complete history—managed from one secure laboratory workspace.</p>
                    </div>
                    <p className="relative text-xs text-[#82aaa5]">MediLab Diagnostics · Bosan Road, Multan</p>
                </div>
                <div className="px-6 py-8 sm:px-12 sm:py-12">
                    <Link href="/" className="mb-8 flex items-center gap-3 lg:hidden"><ApplicationLogo className="h-12 w-12 object-contain"/><span className="font-[Manrope] text-lg font-bold text-[#0f3d39]">MediLab Pro</span></Link>
                    {children}
                </div>
            </div>
        </div>
    );
}
