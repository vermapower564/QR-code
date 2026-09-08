import express from 'express';
import QRCode from 'qrcode';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const app = express();
const PORT = process.env.PORT || 8000;

app.use(express.json());
app.use(express.urlencoded({ extended: true }));
app.use(express.static(path.join(__dirname, 'public')));

// 3 Realistic System Profiles
let profiles = [
    {
        id: 1,
        slug: 'john-doe',
        name: 'John Doe',
        designation: 'CEO & Founder',
        company: 'ABC Technologies',
        bio: 'Technology entrepreneur helping businesses build modern digital solutions.',
        phone: '+1 999 999 9999',
        email: 'john@abctechnologies.com',
        website: 'https://abctechnologies.com',
        status: 'active',
        theme_data: { bg_color: '#f8fafc', text_color: '#0f172a', button_style: 'rounded-xl', theme_preset: 'Business' },
        scans_count: 124,
        social_links: [
            { id: 101, platform: 'website', title: 'Company Website', url: 'https://abctechnologies.com' },
            { id: 102, platform: 'linkedin', title: 'LinkedIn', url: 'https://linkedin.com/in/johndoe' },
            { id: 103, platform: 'instagram', title: 'Instagram', url: 'https://instagram.com/johndoe' },
            { id: 104, platform: 'youtube', title: 'YouTube', url: 'https://youtube.com/@johndoetech' },
            { id: 105, platform: 'whatsapp', title: 'WhatsApp', url: 'https://wa.me/19999999999' }
        ],
        custom_links: [
            { id: 201, title: 'Book a Meeting', description: 'Schedule a 30-min strategy call on Calendly', url: 'https://calendly.com/johndoe' },
            { id: 202, title: 'Company Website', description: 'Explore ABC Technologies solutions & services', url: 'https://abctechnologies.com' },
            { id: 203, title: 'Our Services', description: 'Enterprise software, web applications & cloud development', url: 'https://abctechnologies.com/services' },
            { id: 204, title: 'Contact Us', description: 'Get in touch with our sales & consulting team', url: 'https://abctechnologies.com/contact' }
        ]
    },
    {
        id: 2,
        slug: 'sarah-sharma',
        name: 'Sarah Sharma',
        designation: 'Content Creator',
        company: 'Independent Creator',
        bio: 'Sharing technology, productivity and creative content.',
        phone: '+91 98765 43210',
        email: 'sarah@creatorstudio.com',
        website: 'https://youtube.com/@sarahsharma',
        status: 'active',
        theme_data: { bg_color: '#faf5ff', text_color: '#3b0764', button_style: 'rounded-full', theme_preset: 'Creator' },
        scans_count: 860,
        social_links: [
            { id: 106, platform: 'instagram', title: 'Instagram', url: 'https://instagram.com/sarahsharma' },
            { id: 107, platform: 'youtube', title: 'YouTube Channel', url: 'https://youtube.com/@sarahsharma' },
            { id: 108, platform: 'tiktok', title: 'TikTok', url: 'https://tiktok.com/@sarahsharma' },
            { id: 109, platform: 'twitter', title: 'X / Twitter', url: 'https://twitter.com/sarahsharma' },
            { id: 110, platform: 'threads', title: 'Threads', url: 'https://threads.net/@sarahsharma' }
        ],
        custom_links: [
            { id: 205, title: 'Latest Video', description: 'Watch my latest video on YouTube', url: 'https://youtube.com/watch?v=demo' },
            { id: 206, title: 'My Newsletter', description: 'Weekly tech & productivity digest', url: 'https://sarahsharma.substack.com' },
            { id: 207, title: 'YouTube Channel', description: 'Subscribe for weekly tutorials & reviews', url: 'https://youtube.com/@sarahsharma' },
            { id: 208, title: 'Collaboration', description: 'Sponsorships, speaking & brand partnerships', url: 'mailto:sarah@creatorstudio.com' }
        ]
    },
    {
        id: 3,
        slug: 'alex-verma',
        name: 'Alex Verma',
        designation: 'Full-Stack Developer',
        company: 'Independent Developer',
        bio: 'Building scalable web applications and digital products.',
        phone: '+91 99988 87776',
        email: 'alex@vermacode.dev',
        website: 'https://vermacode.dev',
        status: 'active',
        theme_data: { bg_color: '#0f172a', text_color: '#f8fafc', button_style: 'rounded-xl', theme_preset: 'Dark' },
        scans_count: 450,
        social_links: [
            { id: 111, platform: 'github', title: 'GitHub Profile', url: 'https://github.com/alexverma' },
            { id: 112, platform: 'linkedin', title: 'LinkedIn', url: 'https://linkedin.com/in/alexverma' },
            { id: 113, platform: 'website', title: 'Portfolio', url: 'https://vermacode.dev' },
            { id: 114, platform: 'youtube', title: 'Coding Tutorials', url: 'https://youtube.com/@alexvermacode' },
            { id: 115, platform: 'twitter', title: 'X / Twitter', url: 'https://twitter.com/alexvermacode' }
        ],
        custom_links: [
            { id: 209, title: 'View Portfolio', description: 'Explore my full-stack web & API projects', url: 'https://vermacode.dev/portfolio' },
            { id: 210, title: 'Hire Me', description: 'Available for contract & freelance software projects', url: 'https://vermacode.dev/hire' },
            { id: 211, title: 'My Projects', description: 'Open source packages & SaaS tools on GitHub', url: 'https://github.com/alexverma' },
            { id: 212, title: 'Download Resume', description: 'Full-Stack Software Engineer CV (PDF)', url: 'https://vermacode.dev/resume.pdf' }
        ]
    }
];

function htmlWrapper(title, content) {
    return `<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>${title} - QR Identity</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
</head>
<body class="bg-slate-50 text-slate-900 font-sans antialiased">
    <header class="sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 h-16 flex items-center justify-between">
            <a href="/" class="flex items-center gap-2 text-xl font-bold text-slate-900">
                <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-sky-500 to-indigo-600 flex items-center justify-center text-white font-black text-sm">
                    <i class="fa-solid fa-qrcode"></i>
                </div>
                <span>QR Identity</span>
            </a>
            <nav class="hidden md:flex items-center gap-6 text-sm font-semibold text-slate-600">
                <a href="/pricing" class="hover:text-slate-900">Pricing</a>
                <a href="/about" class="hover:text-slate-900">About</a>
                <a href="/contact" class="hover:text-slate-900">Contact</a>
            </nav>
            <div class="flex items-center gap-4">
                <a href="/login" class="text-sm font-semibold text-slate-700 hover:text-slate-900">Sign in</a>
                <a href="/register" class="px-4 py-2 text-sm font-semibold text-white bg-sky-600 rounded-xl hover:bg-sky-700 shadow-sm">
                    Create Your QR
                </a>
            </div>
        </div>
    </header>
    <main>${content}</main>
    <footer class="bg-slate-900 text-slate-400 py-8 text-center text-xs border-t border-slate-800 space-x-4">
        <a href="/terms" class="hover:underline">Terms</a>
        <a href="/privacy" class="hover:underline">Privacy</a>
        <a href="/cookie-policy" class="hover:underline">Cookies</a>
        <a href="/refund-policy" class="hover:underline">Refund Policy</a>
    </footer>
</body>
</html>`;
}

// Public Pages
app.get('/', (req, res) => {
    res.send(htmlWrapper('Dynamic QR Social Profile SaaS', `
    <section class="py-20 text-center bg-gradient-to-b from-sky-50 to-white px-4">
        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-sky-100 text-sky-700 text-xs font-semibold uppercase mb-6">
            <i class="fa-solid fa-bolt"></i> Account Creation & Dynamic QR Platform
        </div>
        <h1 class="text-4xl sm:text-6xl font-black tracking-tight max-w-4xl mx-auto leading-tight text-slate-900">
            Create your digital identity. <br/>
            <span class="bg-gradient-to-r from-sky-600 to-indigo-600 bg-clip-text text-transparent">One QR code</span> for all your links.
        </h1>
        <p class="mt-6 text-lg text-slate-600 max-w-2xl mx-auto">
            Connect your website, Instagram, LinkedIn, WhatsApp, and contact card under a single dynamic QR code.
        </p>

        <div class="mt-10 flex flex-wrap justify-center gap-4">
            <a href="/register" class="px-8 py-4 bg-sky-600 text-white font-bold rounded-2xl shadow-lg hover:bg-sky-700 transition">
                Create Your QR <i class="fa-solid fa-arrow-right ml-2 text-sm"></i>
            </a>
            <a href="/p/john-doe" target="_blank" class="px-8 py-4 bg-slate-100 text-slate-800 font-bold rounded-2xl hover:bg-slate-200 transition">
                View Live Demo Profile
            </a>
        </div>
    </section>
    `));
});

app.get('/pricing', (req, res) => res.send(htmlWrapper('Pricing', `<div class="max-w-4xl mx-auto py-16 px-4 text-center"><h1 class="text-3xl font-bold mb-4">Pricing Plans</h1><p class="text-slate-600">Starter Free ($0), Pro Creator ($9/mo), Business ($29/mo)</p></div>`)));
app.get('/about', (req, res) => res.send(htmlWrapper('About Us', `<div class="max-w-4xl mx-auto py-16 px-4"><h1 class="text-3xl font-bold mb-4">About QR Identity</h1><p class="text-slate-600">Enterprise dynamic QR profile SaaS platform.</p></div>`)));
app.get('/contact', (req, res) => {
    const successMsg = req.query.submitted ? '<div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-2xl text-sm font-semibold flex items-center gap-3"><i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i> Thank you for reaching out! Our support team will get back to you shortly.</div>' : '';

    res.send(htmlWrapper('Contact Support', `
    <div class="bg-slate-50 min-h-screen py-12 px-4 max-w-7xl mx-auto space-y-12">
        <div class="text-center max-w-3xl mx-auto">
            <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-sky-100 text-sky-700 text-xs font-bold uppercase tracking-wider mb-4">
                <i class="fa-solid fa-headset"></i> Support & Guidance
            </div>
            <h1 class="text-3xl sm:text-5xl font-black text-slate-900 tracking-tight">Contact Support</h1>
            <p class="mt-3 text-base text-slate-600 leading-relaxed">
                Have a question about your account, QR profile, analytics, or billing? We're here to help.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <div class="lg:col-span-5 space-y-4">
                <h2 class="text-xs font-extrabold uppercase tracking-wider text-slate-500 mb-2 px-1">How can we assist you?</h2>

                <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm hover:shadow-md transition">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-2xl bg-sky-100 text-sky-600 font-bold text-lg flex items-center justify-center shrink-0"><i class="fa-solid fa-user-gear"></i></div>
                        <div class="flex-1">
                            <h3 class="text-base font-bold text-slate-900">Account & Profile Support</h3>
                            <p class="text-xs text-slate-500 mt-1">Need help creating or managing your QR profile?</p>
                            <div class="flex flex-wrap gap-1.5 mt-3 mb-4">
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 text-[11px] font-medium">Account creation</span>
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 text-[11px] font-medium">Profile editing</span>
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 text-[11px] font-medium">Social links</span>
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 text-[11px] font-medium">Custom links</span>
                            </div>
                            <a href="#contact-form" class="text-xs font-bold text-sky-600 hover:text-sky-700 inline-flex items-center gap-1.5">Get Account Help <i class="fa-solid fa-arrow-right text-[10px]"></i></a>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm hover:shadow-md transition">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-2xl bg-indigo-100 text-indigo-600 font-bold text-lg flex items-center justify-center shrink-0"><i class="fa-solid fa-qrcode"></i></div>
                        <div class="flex-1">
                            <h3 class="text-base font-bold text-slate-900">QR & Technical Support</h3>
                            <p class="text-xs text-slate-500 mt-1">Having trouble with your QR code or public profile?</p>
                            <div class="flex flex-wrap gap-1.5 mt-3 mb-4">
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 text-[11px] font-medium">QR generation</span>
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 text-[11px] font-medium">QR download</span>
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 text-[11px] font-medium">Public profile</span>
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 text-[11px] font-medium">QR scanning</span>
                            </div>
                            <a href="#contact-form" class="text-xs font-bold text-indigo-600 hover:text-indigo-700 inline-flex items-center gap-1.5">Get Technical Help <i class="fa-solid fa-arrow-right text-[10px]"></i></a>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm hover:shadow-md transition">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-600 font-bold text-lg flex items-center justify-center shrink-0"><i class="fa-solid fa-credit-card"></i></div>
                        <div class="flex-1">
                            <h3 class="text-base font-bold text-slate-900">Billing & Subscription</h3>
                            <p class="text-xs text-slate-500 mt-1">Questions about your plan or payments?</p>
                            <div class="flex flex-wrap gap-1.5 mt-3 mb-4">
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 text-[11px] font-medium">Free plan</span>
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 text-[11px] font-medium">Pro plan</span>
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 text-[11px] font-medium">Business plan</span>
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 text-[11px] font-medium">Invoices</span>
                            </div>
                            <a href="#contact-form" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 inline-flex items-center gap-1.5">Get Billing Help <i class="fa-solid fa-arrow-right text-[10px]"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-7" id="contact-form">
                <div class="bg-white p-8 sm:p-10 rounded-3xl border border-slate-200 shadow-xl">
                    <div class="mb-6">
                        <h2 class="text-2xl font-black text-slate-900">Send us a message</h2>
                        <p class="text-xs text-slate-500 mt-1">Fill in the details below and our team will get back to you shortly.</p>
                    </div>

                    ${successMsg}

                    <form action="/contact" method="POST" class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Your Name *</label>
                                <input type="text" name="name" required placeholder="e.g. John Doe" class="w-full p-3 rounded-xl border text-sm outline-none"/>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Email Address *</label>
                                <input type="email" name="email" required placeholder="john@example.com" class="w-full p-3 rounded-xl border text-sm outline-none"/>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Subject *</label>
                            <input type="text" name="subject" required placeholder="Subject or Topic..." class="w-full p-3 rounded-xl border text-sm outline-none"/>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Message *</label>
                            <textarea name="message" rows="5" required placeholder="Describe your question or issue..." class="w-full p-3 rounded-xl border text-sm outline-none"></textarea>
                        </div>
                        <button type="submit" class="w-full py-4 bg-sky-600 text-white font-bold text-sm rounded-xl shadow-md">Send Message</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="pt-8 border-t border-slate-200">
            <div class="text-center mb-8">
                <h2 class="text-2xl font-black text-slate-900">Common Questions</h2>
                <p class="text-xs text-slate-500 mt-1">Quick answers to frequently asked questions</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-5xl mx-auto">
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                    <h3 class="text-sm font-extrabold text-slate-900 mb-1 flex items-center gap-2"><i class="fa-solid fa-circle-question text-sky-600"></i> How do I create my dynamic QR profile?</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">Sign up for a free account, enter profile details, choose a template, and generate your dynamic QR code instantly.</p>
                </div>
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                    <h3 class="text-sm font-extrabold text-slate-900 mb-1 flex items-center gap-2"><i class="fa-solid fa-circle-question text-sky-600"></i> Can I update links without changing my QR code?</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">Yes! Dynamic QR codes encode your profile URL. You can update social networks or custom links anytime without re-printing.</p>
                </div>
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                    <h3 class="text-sm font-extrabold text-slate-900 mb-1 flex items-center gap-2"><i class="fa-solid fa-circle-question text-sky-600"></i> What formats can I download my QR code in?</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">Download PNG images (512px to 2048px), vector SVG graphics, or branded PDF frame documents directly from your dashboard.</p>
                </div>
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                    <h3 class="text-sm font-extrabold text-slate-900 mb-1 flex items-center gap-2"><i class="fa-solid fa-circle-question text-sky-600"></i> How does dynamic QR scanning analytics work?</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">Every scan is tracked anonymously using SHA-256 IP hashing for privacy compliance. View total scans and link clicks in real-time.</p>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-sky-600 to-indigo-700 rounded-3xl p-8 sm:p-10 text-white text-center shadow-xl">
            <h2 class="text-2xl sm:text-3xl font-black mb-2">Ready to create your digital identity?</h2>
            <p class="text-sm text-sky-100 max-w-xl mx-auto mb-6">Join thousands of creators, entrepreneurs, and teams sharing their digital identity with dynamic QR codes.</p>
            <a href="/register" class="inline-flex items-center gap-2 px-8 py-4 bg-white text-slate-900 font-bold rounded-2xl shadow-lg hover:bg-slate-100 transition text-sm"><span>Create Your QR</span> <i class="fa-solid fa-arrow-right"></i></a>
        </div>
    </div>
    `));
});

app.post('/contact', (req, res) => {
    res.redirect('/contact?submitted=true');
});
app.get('/terms', (req, res) => res.send(htmlWrapper('Terms', `<div class="max-w-4xl mx-auto py-16 px-4"><h1 class="text-3xl font-bold mb-4">Terms & Conditions</h1></div>`)));
app.get('/privacy', (req, res) => res.send(htmlWrapper('Privacy', `<div class="max-w-4xl mx-auto py-16 px-4"><h1 class="text-3xl font-bold mb-4">Privacy Policy</h1></div>`)));
app.get('/cookie-policy', (req, res) => res.send(htmlWrapper('Cookie Policy', `<div class="max-w-4xl mx-auto py-16 px-4"><h1 class="text-3xl font-bold mb-4">Cookie Policy</h1></div>`)));
app.get('/refund-policy', (req, res) => res.send(htmlWrapper('Refund Policy', `<div class="max-w-4xl mx-auto py-16 px-4"><h1 class="text-3xl font-bold mb-4">Refund Policy</h1></div>`)));

// Onboarding State
let currentUser = {
    id: 99,
    name: 'New Creator',
    email: 'newcreator@example.com',
    onboarding_completed: false
};

// Registration Page
app.get('/register', (req, res) => {
    res.send(htmlWrapper('Create Account', `
    <div class="min-h-[85vh] flex items-center justify-center py-12 px-4">
        <div class="max-w-md w-full bg-white p-8 sm:p-10 rounded-3xl shadow-xl border border-slate-200 text-center"
             x-data="{ 
                submitting: false, 
                showPassword: false, 
                showConfirmPassword: false,
                password: '',
                get strength() {
                    if (!this.password) return { label: '', color: '', percent: 0 };
                    let score = 0;
                    if (this.password.length >= 8) score++;
                    if (/[A-Z]/.test(this.password)) score++;
                    if (/[a-z]/.test(this.password)) score++;
                    if (/[0-9]/.test(this.password)) score++;
                    if (/[^A-Za-z0-9]/.test(this.password)) score++;
                    if (score <= 2) return { label: 'Weak', color: 'bg-rose-500 text-rose-700', percent: 33 };
                    if (score <= 4) return { label: 'Medium', color: 'bg-amber-500 text-amber-700', percent: 66 };
                    return { label: 'Strong', color: 'bg-emerald-500 text-emerald-700', percent: 100 };
                }
             }">
            <h2 class="text-2xl font-black mb-1">Create your account</h2>
            <p class="text-xs text-slate-500 mb-6">Create your digital profile and generate your dynamic QR code.</p>
            <form action="/register" method="POST" @submit="submitting = true" class="space-y-4 text-left">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Full Name *</label>
                    <input type="text" name="name" placeholder="e.g. John Doe" required class="w-full p-3 rounded-xl border text-sm outline-none"/>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Email Address *</label>
                    <input type="email" name="email" placeholder="john@example.com" required class="w-full p-3 rounded-xl border text-sm outline-none"/>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Company</label>
                        <input type="text" name="company" placeholder="ABC Corp" class="w-full p-3 rounded-xl border text-sm outline-none"/>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Phone</label>
                        <input type="text" name="phone" placeholder="+123456789" class="w-full p-3 rounded-xl border text-sm outline-none"/>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Password *</label>
                    <div class="relative">
                        <input :type="showPassword ? 'text' : 'password'" name="password" x-model="password" required placeholder="e.g. Roushan@123" class="w-full p-3 pr-10 rounded-xl border text-sm outline-none"/>
                        <button type="button" @click="showPassword = !showPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-sm">
                            <i class="fa-solid" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                    </div>
                    <div x-show="password.length > 0" class="mt-2 space-y-1" x-transition>
                        <div class="flex items-center justify-between text-[11px] font-bold">
                            <span class="text-slate-500">Password Strength:</span>
                            <span :class="strength.color" x-text="strength.label" class="px-1.5 py-0.5 rounded"></span>
                        </div>
                        <div class="w-full bg-slate-200 h-1.5 rounded-full overflow-hidden">
                            <div class="h-full transition-all duration-300" :class="strength.color" :style="\`width: \${strength.percent}%\`"></div>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Confirm Password *</label>
                    <div class="relative">
                        <input :type="showConfirmPassword ? 'text' : 'password'" name="password_confirmation" required placeholder="Re-enter password" class="w-full p-3 pr-10 rounded-xl border text-sm outline-none"/>
                        <button type="button" @click="showConfirmPassword = !showConfirmPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-sm">
                            <i class="fa-solid" :class="showConfirmPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                    </div>
                </div>
                <button type="submit" :disabled="submitting" class="w-full py-3.5 bg-sky-600 text-white font-bold rounded-xl text-sm shadow disabled:opacity-50 flex items-center justify-center gap-2">
                    <span x-show="!submitting">Create Account</span>
                    <span x-show="submitting"><i class="fa-solid fa-circle-notch fa-spin"></i> Creating Account...</span>
                </button>
            </form>
            <p class="text-xs text-slate-600 mt-6">Already have an account? <a href="/login" class="font-bold text-sky-600">Log in</a></p>
        </div>
    </div>
    `));
});

app.post('/register', (req, res) => {
    currentUser.onboarding_completed = false;
    res.redirect('/email/verify');
});

// Email Verification Notice Page
app.get('/email/verify', (req, res) => {
    res.send(htmlWrapper('Verify Email Address', `
    <div class="min-h-[75vh] flex items-center justify-center py-12 px-4">
        <div class="max-w-md w-full bg-white p-8 rounded-3xl shadow-xl border border-slate-200 text-center">
            <h2 class="text-2xl font-black mb-2">Verify your email address</h2>
            <p class="text-xs text-slate-500 mb-6">Please check your inbox for a verification link.</p>
            <a href="/onboarding" class="w-full py-3.5 px-4 bg-sky-600 text-white font-bold rounded-xl text-sm block shadow">Resend Verification Email / Continue to Onboarding</a>
        </div>
    </div>
    `));
});

app.get('/login', (req, res) => {
    res.send(htmlWrapper('Log In', `
    <div class="min-h-[75vh] flex items-center justify-center py-12 px-4">
        <div class="max-w-md w-full bg-white p-8 rounded-3xl shadow-xl border border-slate-200 text-center" x-data="{ submitting: false, showPassword: false }">
            <h2 class="text-2xl font-black mb-1">Welcome Back</h2>
            <p class="text-xs text-slate-500 mb-6">Sign in to manage your QR profiles & analytics</p>
            <form action="/login" method="POST" @submit="submitting = true" class="space-y-4 text-left">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Email Address *</label>
                    <input type="email" name="email" required placeholder="john@example.com" class="w-full p-3 rounded-xl border text-sm outline-none"/>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Password *</label>
                    <div class="relative">
                        <input :type="showPassword ? 'text' : 'password'" name="password" required placeholder="••••••••" class="w-full p-3 pr-10 rounded-xl border text-sm outline-none"/>
                        <button type="button" @click="showPassword = !showPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-sm">
                            <i class="fa-solid" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                    </div>
                </div>
                <button type="submit" :disabled="submitting" class="w-full text-center py-3.5 bg-sky-600 text-white font-bold rounded-xl text-sm shadow disabled:opacity-50 flex items-center justify-center gap-2">
                    <span x-show="!submitting">Login</span>
                    <span x-show="submitting"><i class="fa-solid fa-circle-notch fa-spin"></i> Signing in...</span>
                </button>
            </form>
            <div class="flex items-center justify-between text-xs font-semibold mt-4">
                <a href="/forgot-password" class="text-sky-600">Forgot Password?</a>
                <a href="/register" class="text-sky-600">Create Account</a>
            </div>
        </div>
    </div>
    `));
});

app.post('/login', (req, res) => {
    if (!currentUser.onboarding_completed) {
        return res.redirect('/onboarding');
    }
    return res.redirect('/dashboard');
});

// Onboarding Entry Route
app.get('/onboarding', (req, res) => {
    if (currentUser.onboarding_completed) {
        return res.redirect('/dashboard');
    }

    res.send(htmlWrapper('Complete Your Profile', `
    <div class="min-h-[85vh] bg-slate-50 py-12 px-4 max-w-2xl mx-auto">
        <div class="mb-8 text-center">
            <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-sky-100 text-sky-700 text-xs font-bold uppercase tracking-wider mb-3">
                <i class="fa-solid fa-wand-magic-sparkles"></i> Step 2 of 2: Create Your First QR Profile
            </div>
            <h1 class="text-3xl font-black text-slate-900">Set Up Your Dynamic Identity</h1>
            <p class="text-sm text-slate-600 mt-1">Fill in your profile details to generate your dynamic QR code.</p>
        </div>

        <div class="bg-white p-8 rounded-3xl shadow-xl border border-slate-200">
            <form action="/onboarding" method="POST" class="space-y-5">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Full Name *</label>
                    <input type="text" name="name" value="Demo Creator" required class="w-full p-3 rounded-xl border text-sm"/>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Username / Slug *</label>
                    <div class="flex rounded-xl border overflow-hidden">
                        <span class="bg-slate-100 text-slate-500 text-xs font-semibold px-3 flex items-center">/p/</span>
                        <input type="text" name="username" value="demo-creator" required class="w-full p-3 text-sm border-none"/>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Designation</label>
                        <input type="text" name="designation" value="Product Designer" class="w-full p-3 rounded-xl border text-sm"/>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Company</label>
                        <input type="text" name="company" value="Design Studio" class="w-full p-3 rounded-xl border text-sm"/>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Bio</label>
                    <textarea name="bio" rows="2" class="w-full p-3 rounded-xl border text-sm">Building clean digital products & dynamic QR experiences.</textarea>
                </div>
                <button type="submit" class="w-full py-4 bg-sky-600 text-white font-bold text-sm rounded-xl shadow-lg">Save Profile & Generate Dynamic QR</button>
            </form>
        </div>
    </div>
    `));
});

app.post('/onboarding', (req, res) => {
    const slug = req.body.username || 'demo-creator';
    const name = req.body.name || 'Demo Creator';
    
    let existing = profiles.find(p => p.slug === slug);
    if (!existing) {
        existing = {
            id: profiles.length + 1,
            slug: slug,
            name: name,
            designation: req.body.designation || 'Creator',
            company: req.body.company || 'Independent',
            bio: req.body.bio || 'Dynamic QR Profile',
            phone: '+1 800 555 0199',
            email: 'creator@example.com',
            website: 'https://example.com',
            status: 'active',
            theme_data: { bg_color: '#f8fafc', text_color: '#0f172a', button_style: 'rounded-xl', theme_preset: 'Business' },
            scans_count: 0,
            social_links: [
                { id: 301, platform: 'website', title: 'Website', url: 'https://example.com' }
            ],
            custom_links: [
                { id: 401, title: 'Book Meeting', description: 'Schedule a call', url: 'https://calendly.com' }
            ]
        };
        profiles.push(existing);
    }
    
    currentUser.onboarding_completed = true;
    res.redirect('/onboarding/complete');
});

// Onboarding Completion Screen Route
app.get('/onboarding/complete', (req, res) => {
    const profile = profiles[profiles.length - 1] || profiles[0];

    res.send(htmlWrapper('Your QR Profile is Ready', `
    <div class="min-h-[85vh] bg-slate-50 flex items-center justify-center py-12 px-4">
        <div class="max-w-xl w-full bg-white p-8 rounded-3xl shadow-2xl border border-slate-200 text-center">
            <div class="w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 font-black text-2xl flex items-center justify-center mx-auto mb-4">
                <i class="fa-solid fa-check"></i>
            </div>
            <h1 class="text-3xl font-black text-slate-900">Your QR profile is ready.</h1>
            <p class="text-lg font-bold text-sky-600 mt-1">${profile.name}</p>
            <p class="text-sm text-slate-500 mt-0.5">Your digital profile is now live.</p>

            <div class="my-8 bg-slate-50 p-6 rounded-2xl border border-slate-200 inline-block">
                <div class="bg-white p-4 rounded-xl shadow-md inline-block">
                    <img src="/api/qr/${profile.slug}" alt="Dynamic QR Code" class="w-48 h-48 mx-auto"/>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 mb-4">
                <a href="/p/${profile.slug}" target="_blank" class="py-3 bg-slate-900 text-white font-bold rounded-xl text-sm">View Profile</a>
                <a href="/api/qr/${profile.slug}" download="${profile.slug}-qr.png" class="py-3 bg-emerald-600 text-white font-bold rounded-xl text-sm">Download QR</a>
                <a href="#" onclick="alert('Profile link copied: ' + window.location.origin + '/p/${profile.slug}')" class="py-3 bg-slate-100 text-slate-800 font-bold rounded-xl text-sm">Share QR</a>
                <a href="/dashboard" class="py-3 bg-sky-600 text-white font-bold rounded-xl text-sm">Go to Dashboard</a>
            </div>
        </div>
    </div>
    `));
});

app.get('/forgot-password', (req, res) => {
    res.send(htmlWrapper('Forgot Password', `
    <div class="min-h-[75vh] flex items-center justify-center py-12 px-4">
        <div class="max-w-md w-full bg-white p-8 rounded-3xl shadow-xl border border-slate-200 text-center">
            <h2 class="text-2xl font-black mb-2">Forgot Password</h2>
            <p class="text-xs text-slate-500 mb-6">Enter your email for a password reset link.</p>
            <form action="/forgot-password" method="POST" class="space-y-4 text-left">
                <input type="email" placeholder="Email Address" required class="w-full p-3 rounded-xl border text-sm"/>
                <a href="/login" class="block w-full text-center py-3 bg-sky-600 text-white font-bold rounded-xl text-sm shadow">Send Password Reset Link</a>
            </form>
        </div>
    </div>
    `));
});

// Dynamic QR Code API Endpoint
app.get('/api/qr/:slug', async (req, res) => {
    const slug = req.params.slug;
    const fg = req.query.fg || '#000000';
    const bg = req.query.bg || '#ffffff';
    const profileUrl = `${req.protocol}://${req.get('host')}/p/${slug}`;

    try {
        const qrDataUrl = await QRCode.toDataURL(profileUrl, {
            color: { dark: fg, light: bg },
            width: 512,
            margin: 2
        });
        const base64Data = qrDataUrl.replace(/^data:image\/png;base64,/, "");
        const img = Buffer.from(base64Data, 'base64');

        res.writeHead(200, {
            'Content-Type': 'image/png',
            'Content-Length': img.length
        });
        res.end(img);
    } catch (err) {
        res.status(500).send("QR Generation Error");
    }
});

// Public Profile (/p/:slug)
app.get('/p/:slug', (req, res) => {
    const profile = profiles.find(p => p.slug === req.params.slug);
    if (!profile) {
        return res.status(404).send(htmlWrapper('Profile Not Found', `
        <div class="min-h-screen flex flex-col items-center justify-center text-center p-6">
            <h2 class="text-2xl font-bold">Profile Not Found</h2>
            <p class="text-slate-500 mt-2">No active profile exists with this username.</p>
            <a href="/" class="mt-6 px-6 py-3 bg-sky-600 text-white font-bold rounded-xl">Return Home</a>
        </div>
        `));
    }

    profile.scans_count++;
    const bg = profile.theme_data.bg_color || '#f8fafc';
    const text = profile.theme_data.text_color || '#0f172a';

    res.send(`<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>${profile.name} | Dynamic Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
</head>
<body style="background-color: ${bg}; color: ${text};" class="min-h-screen font-sans p-6 flex flex-col justify-between">
    <div class="max-w-md w-full mx-auto pt-6">
        <div class="text-center mb-6">
            <div class="w-28 h-28 rounded-full bg-gradient-to-tr from-sky-500 to-indigo-600 text-white font-black text-3xl flex items-center justify-center mx-auto shadow-lg mb-4">
                ${profile.name.charAt(0)}
            </div>
            <h1 class="text-2xl font-black">${profile.name}</h1>
            <p class="text-sm font-semibold opacity-80 mt-1">${profile.designation} • ${profile.company}</p>
            <p class="text-sm opacity-70 mt-3 max-w-xs mx-auto">${profile.bio}</p>
        </div>

        <div class="grid grid-cols-3 gap-2.5 mb-6">
            <a href="tel:${profile.phone}" class="bg-white/20 border border-black/10 p-3 rounded-2xl text-center shadow-sm hover:scale-105 transition">
                <i class="fa-solid fa-phone text-emerald-500 text-lg block mb-1"></i>
                <span class="text-xs font-bold block">Call</span>
            </a>
            <a href="mailto:${profile.email}" class="bg-white/20 border border-black/10 p-3 rounded-2xl text-center shadow-sm hover:scale-105 transition">
                <i class="fa-solid fa-envelope text-sky-500 text-lg block mb-1"></i>
                <span class="text-xs font-bold block">Email</span>
            </a>
            <a href="/p/${profile.slug}/contact" class="bg-white/20 border border-black/10 p-3 rounded-2xl text-center shadow-sm hover:scale-105 transition">
                <i class="fa-solid fa-address-book text-indigo-500 text-lg block mb-1"></i>
                <span class="text-xs font-bold block">VCF</span>
            </a>
        </div>

        <a href="/p/${profile.slug}/contact" class="w-full py-4 px-6 bg-gradient-to-r from-sky-600 to-indigo-600 text-white font-bold text-sm rounded-2xl flex items-center justify-center gap-3 shadow-lg mb-8">
            <i class="fa-solid fa-user-plus text-lg"></i> Save Contact to Phone (.vcf)
        </a>

        <div class="space-y-3 mb-8">
            <h3 class="text-xs uppercase font-bold tracking-widest opacity-60 px-1">Social Networks</h3>
            ${profile.social_links.map(l => `
                <a href="${l.url}" target="_blank" class="w-full p-4 bg-white border border-slate-200 rounded-2xl flex items-center justify-between font-bold text-sm text-slate-900 shadow-sm hover:translate-y-[-2px] transition">
                    <div class="flex items-center gap-3">
                        <i class="fa-brands fa-${l.platform === 'website' ? 'globe' : l.platform} text-xl text-sky-600"></i>
                        <span>${l.title}</span>
                    </div>
                    <i class="fa-solid fa-chevron-right text-xs opacity-40"></i>
                </a>
            `).join('')}
        </div>

        <div class="space-y-3 mb-8">
            <h3 class="text-xs uppercase font-bold tracking-widest opacity-60 px-1">Featured Buttons</h3>
            ${profile.custom_links.map(l => `
                <a href="${l.url}" target="_blank" class="w-full p-4 bg-slate-900 text-white rounded-2xl flex items-center justify-between font-bold text-sm shadow-md">
                    <div>
                        <span class="block">${l.title}</span>
                        <span class="text-xs opacity-75 block mt-0.5">${l.description}</span>
                    </div>
                    <i class="fa-solid fa-arrow-up-right-from-square text-xs opacity-70"></i>
                </a>
            `).join('')}
        </div>
    </div>

    <footer class="text-center py-6 text-xs opacity-60">
        Powered by <a href="/" class="font-bold underline">QR Identity</a>
    </footer>
</body>
</html>`);
});

// Contact Card (.vcf Download)
app.get('/p/:slug/contact', (req, res) => {
    const profile = profiles.find(p => p.slug === req.params.slug);
    if (!profile) return res.status(404).send("Not Found");

    const vcf = `BEGIN:VCARD
VERSION:3.0
FN:${profile.name}
ORG:${profile.company}
TITLE:${profile.designation}
TEL;TYPE=CELL:${profile.phone}
EMAIL;TYPE=INTERNET:${profile.email}
URL:${profile.website}
NOTE:${profile.bio}
END:VCARD`;

    res.setHeader('Content-Type', 'text/vcard');
    res.setHeader('Content-Disposition', `attachment; filename="${profile.slug}.vcf"`);
    res.send(vcf);
});

// Dashboard (/dashboard)
app.get('/dashboard', (req, res) => {
    res.send(htmlWrapper('Dashboard Overview', `
    <div class="min-h-screen flex bg-slate-100">
        <aside class="w-64 bg-slate-900 text-slate-300 p-6 flex flex-col justify-between hidden md:flex">
            <div>
                <div class="flex items-center gap-3 text-white text-xl font-bold mb-8">
                    <div class="w-9 h-9 rounded-xl bg-sky-500 flex items-center justify-center text-white text-base">
                        <i class="fa-solid fa-qrcode"></i>
                    </div>
                    <span>QR Identity</span>
                </div>
                <nav class="space-y-2 font-medium text-sm">
                    <a href="/dashboard" class="flex items-center gap-3 px-3 py-2.5 bg-sky-600 text-white rounded-xl"><i class="fa-solid fa-chart-pie"></i> Dashboard</a>
                    <a href="/dashboard/billing" class="flex items-center gap-3 px-3 py-2.5 hover:bg-slate-800 text-slate-400 rounded-xl"><i class="fa-solid fa-credit-card"></i> Billing Module</a>
                    <a href="/p/john-doe" target="_blank" class="flex items-center gap-3 px-3 py-2.5 hover:bg-slate-800 text-slate-400 rounded-xl"><i class="fa-solid fa-id-card"></i> Ex 1: John Doe</a>
                </nav>
            </div>
            <a href="/" class="text-sm font-semibold text-rose-400"><i class="fa-solid fa-right-from-bracket mr-2"></i> Exit Server</a>
        </aside>

        <main class="flex-1 p-8 max-w-6xl">
            <div class="flex items-center justify-between mb-8">
                <h1 class="text-2xl font-bold">Dashboard Overview</h1>
                <a href="/dashboard/billing" class="px-4 py-2 bg-sky-600 text-white font-bold text-xs rounded-xl shadow">Billing Module &rarr;</a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                    <span class="text-xs font-bold text-slate-500 uppercase">Profiles</span>
                    <p class="text-3xl font-black mt-2">${profiles.length}</p>
                </div>
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                    <span class="text-xs font-bold text-slate-500 uppercase">Total Scans</span>
                    <p class="text-3xl font-black mt-2">1,434</p>
                </div>
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                    <span class="text-xs font-bold text-slate-500 uppercase">Unique Visitors</span>
                    <p class="text-3xl font-black mt-2">1,042</p>
                </div>
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                    <span class="text-xs font-bold text-slate-500 uppercase">Link Clicks</span>
                    <p class="text-3xl font-black mt-2">482</p>
                </div>
            </div>
        </main>
    </div>
    `));
});

// Billing Module Routes
app.get('/dashboard/billing', (req, res) => {
    res.send(htmlWrapper('Billing Overview', `
    <div class="min-h-screen bg-slate-50 p-6 sm:p-10 max-w-6xl mx-auto space-y-8">
        <div class="flex items-center justify-between border-b border-slate-200 pb-4">
            <div>
                <h1 class="text-2xl font-black text-slate-900">Billing & Subscriptions</h1>
                <p class="text-xs text-slate-500 mt-1">Manage active plan, payment methods, and invoices.</p>
            </div>
            <div class="flex items-center gap-2 bg-slate-100 p-1.5 rounded-2xl text-xs font-bold">
                <a href="/dashboard/billing" class="px-4 py-2 rounded-xl bg-white text-slate-900 shadow-sm">Overview</a>
                <a href="/dashboard/billing/invoices" class="px-4 py-2 rounded-xl text-slate-600 hover:text-slate-900">Invoices</a>
                <a href="/dashboard/billing/payment-methods" class="px-4 py-2 rounded-xl text-slate-600 hover:text-slate-900">Payment Methods</a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
                <span class="text-xs font-bold text-slate-400 uppercase">Current Plan</span>
                <h3 class="text-xl font-black text-slate-900 mt-1">Business & Teams</h3>
                <p class="text-xs text-slate-500 mt-2">Renews Oct 01, 2026</p>
            </div>
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
                <span class="text-xs font-bold text-slate-400 uppercase">Amount Due</span>
                <h3 class="text-3xl font-black text-slate-900 mt-1">$29.00 <span class="text-xs font-normal text-slate-500">USD</span></h3>
                <p class="text-xs text-emerald-600 font-bold mt-2">Status: Paid</p>
            </div>
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
                <span class="text-xs font-bold text-slate-400 uppercase">Default Payment</span>
                <h4 class="text-sm font-bold text-slate-900 mt-2">Visa •••• 4242</h4>
                <a href="/dashboard/billing/payment-methods" class="text-xs font-bold text-sky-600 mt-1 block">Manage &rarr;</a>
            </div>
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
                <span class="text-xs font-bold text-slate-400 uppercase">Account Status</span>
                <div class="mt-2 inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-bold">Active</div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold">Recent Invoices</h2>
                <a href="/dashboard/billing/invoices" class="text-xs font-bold text-sky-600">View All Invoices &rarr;</a>
            </div>
            <table class="w-full text-left text-sm">
                <thead><tr class="text-xs text-slate-400 uppercase border-b"><th class="pb-2">Invoice #</th><th>Date</th><th>Amount</th><th>Status</th><th class="text-right">Action</th></tr></thead>
                <tbody class="divide-y text-xs font-semibold">
                    <tr><td class="py-3 font-mono font-bold">INV-2026-0001</td><td>Sep 01, 2026</td><td>$29.00 USD</td><td><span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 uppercase">Completed</span></td><td class="text-right"><a href="/dashboard/billing/invoices/1/download" class="px-3 py-1 bg-slate-100 rounded text-slate-700 font-bold">Download</a></td></tr>
                    <tr><td class="py-3 font-mono font-bold">INV-2026-0002</td><td>Aug 01, 2026</td><td>$29.00 USD</td><td><span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 uppercase">Completed</span></td><td class="text-right"><a href="/dashboard/billing/invoices/1/download" class="px-3 py-1 bg-slate-100 rounded text-slate-700 font-bold">Download</a></td></tr>
                </tbody>
            </table>
        </div>
    </div>
    `));
});

app.get('/dashboard/billing/invoices', (req, res) => {
    res.send(htmlWrapper('Invoices', `
    <div class="min-h-screen bg-slate-50 p-6 sm:p-10 max-w-6xl mx-auto space-y-8">
        <div class="flex items-center justify-between border-b border-slate-200 pb-4">
            <div>
                <h1 class="text-2xl font-black text-slate-900">Invoice History</h1>
                <p class="text-xs text-slate-500 mt-1">Search, filter, view details, and download official billing invoices.</p>
            </div>
            <div class="flex items-center gap-2 bg-slate-100 p-1.5 rounded-2xl text-xs font-bold">
                <a href="/dashboard/billing" class="px-4 py-2 rounded-xl text-slate-600 hover:text-slate-900">Overview</a>
                <a href="/dashboard/billing/invoices" class="px-4 py-2 rounded-xl bg-white text-slate-900 shadow-sm">Invoices</a>
                <a href="/dashboard/billing/payment-methods" class="px-4 py-2 rounded-xl text-slate-600 hover:text-slate-900">Payment Methods</a>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
            <table class="w-full text-left text-sm">
                <thead><tr class="text-xs text-slate-400 uppercase border-b"><th class="pb-3">Invoice #</th><th>Date</th><th>Billing Period</th><th>Amount</th><th>Status</th><th class="text-right">Actions</th></tr></thead>
                <tbody class="divide-y text-xs font-semibold">
                    <tr><td class="py-4 font-mono font-bold text-slate-900">INV-2026-0001</td><td>Sep 01, 2026</td><td>Sep 01 - Sep 30, 2026</td><td>$29.00 USD</td><td><span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 rounded-full font-bold uppercase">Paid</span></td><td class="text-right"><a href="/dashboard/billing/invoices/1/download" class="px-3 py-1.5 bg-sky-600 text-white rounded font-bold">Download PDF</a></td></tr>
                    <tr><td class="py-4 font-mono font-bold text-slate-900">INV-2026-0002</td><td>Aug 01, 2026</td><td>Aug 01 - Aug 31, 2026</td><td>$29.00 USD</td><td><span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 rounded-full font-bold uppercase">Paid</span></td><td class="text-right"><a href="/dashboard/billing/invoices/1/download" class="px-3 py-1.5 bg-sky-600 text-white rounded font-bold">Download PDF</a></td></tr>
                </tbody>
            </table>
        </div>
    </div>
    `));
});

app.get('/dashboard/billing/invoices/:id/download', (req, res) => {
    res.setHeader('Content-Type', 'text/plain');
    res.setHeader('Content-Disposition', 'attachment; filename="Invoice-INV-2026-0001.txt"');
    res.send("INVOICE SUMMARY\n==============================\nInvoice #: INV-2026-0001\nAmount Paid: $29.00 USD\nStatus: Paid\nPayment Method: Visa •••• 4242\n==============================\nThank you for using QR Identity SaaS.");
});

app.get('/dashboard/billing/payment-methods', (req, res) => {
    res.send(htmlWrapper('Payment Methods', `
    <div class="min-h-screen bg-slate-50 p-6 sm:p-10 max-w-6xl mx-auto space-y-8">
        <div class="flex items-center justify-between border-b border-slate-200 pb-4">
            <div>
                <h1 class="text-2xl font-black text-slate-900">Payment Methods</h1>
                <p class="text-xs text-slate-500 mt-1">Manage saved credit cards, default payment options, and billing authorization.</p>
            </div>
            <div class="flex items-center gap-2 bg-slate-100 p-1.5 rounded-2xl text-xs font-bold">
                <a href="/dashboard/billing" class="px-4 py-2 rounded-xl text-slate-600 hover:text-slate-900">Overview</a>
                <a href="/dashboard/billing/invoices" class="px-4 py-2 rounded-xl text-slate-600 hover:text-slate-900">Invoices</a>
                <a href="/dashboard/billing/payment-methods" class="px-4 py-2 rounded-xl bg-white text-slate-900 shadow-sm">Payment Methods</a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm relative">
                <span class="absolute top-4 right-4 px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 text-[11px] font-extrabold uppercase">Default Card</span>
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-9 bg-slate-900 text-white rounded-xl flex items-center justify-center font-bold text-sm"><i class="fa-brands fa-cc-visa text-lg"></i></div>
                    <div>
                        <h3 class="text-base font-black text-slate-900">Visa •••• 4242</h3>
                        <p class="text-xs text-slate-500">Expires 12/2028</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    `));
});

app.listen(PORT, () => {
    console.log(`\n==================================================`);
    console.log(`  Dynamic QR Social Profile SaaS Web Server Active`);
    console.log(`  Access Application at: http://localhost:${PORT}`);
    console.log(`==================================================\n`);
});
