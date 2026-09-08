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
                <a href="/dashboard" class="px-4 py-2 text-sm font-semibold text-white bg-sky-600 rounded-xl hover:bg-sky-700 shadow-sm">
                    Dashboard
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
            <i class="fa-solid fa-bolt"></i> Approved Architecture Deployed
        </div>
        <h1 class="text-4xl sm:text-6xl font-black tracking-tight max-w-4xl mx-auto leading-tight text-slate-900">
            Create your digital identity. <br/>
            <span class="bg-gradient-to-r from-sky-600 to-indigo-600 bg-clip-text text-transparent">One QR code</span> for all your links.
        </h1>
        <p class="mt-6 text-lg text-slate-600 max-w-2xl mx-auto">
            Connect your website, Instagram, LinkedIn, WhatsApp, and contact card under a single dynamic QR code.
        </p>

        <div class="mt-10 flex flex-wrap justify-center gap-4">
            <a href="/p/john-doe" target="_blank" class="px-6 py-3.5 bg-sky-600 text-white font-bold rounded-2xl shadow-lg hover:bg-sky-700 transition">
                Ex 1: John Doe (Business)
            </a>
            <a href="/p/sarah-sharma" target="_blank" class="px-6 py-3.5 bg-purple-600 text-white font-bold rounded-2xl shadow-lg hover:bg-purple-700 transition">
                Ex 2: Sarah Sharma (Creator)
            </a>
            <a href="/p/alex-verma" target="_blank" class="px-6 py-3.5 bg-slate-900 text-white font-bold rounded-2xl shadow-lg hover:bg-slate-800 transition">
                Ex 3: Alex Verma (Freelancer)
            </a>
        </div>
    </section>
    `));
});

app.get('/pricing', (req, res) => res.send(htmlWrapper('Pricing', `<div class="max-w-4xl mx-auto py-16 px-4 text-center"><h1 class="text-3xl font-bold mb-4">Pricing Plans</h1><p class="text-slate-600">Starter Free ($0), Pro Creator ($9/mo), Business ($29/mo)</p></div>`)));
app.get('/about', (req, res) => res.send(htmlWrapper('About Us', `<div class="max-w-4xl mx-auto py-16 px-4"><h1 class="text-3xl font-bold mb-4">About QR Identity</h1><p class="text-slate-600">Enterprise dynamic QR profile SaaS platform.</p></div>`)));
app.get('/contact', (req, res) => res.send(htmlWrapper('Contact Us', `<div class="max-w-xl mx-auto py-16 px-4"><h1 class="text-3xl font-bold mb-4">Contact Support</h1><p class="text-slate-600 mb-4">Reach out to our team.</p></div>`)));
app.get('/terms', (req, res) => res.send(htmlWrapper('Terms', `<div class="max-w-4xl mx-auto py-16 px-4"><h1 class="text-3xl font-bold mb-4">Terms & Conditions</h1></div>`)));
app.get('/privacy', (req, res) => res.send(htmlWrapper('Privacy', `<div class="max-w-4xl mx-auto py-16 px-4"><h1 class="text-3xl font-bold mb-4">Privacy Policy</h1></div>`)));
app.get('/cookie-policy', (req, res) => res.send(htmlWrapper('Cookie Policy', `<div class="max-w-4xl mx-auto py-16 px-4"><h1 class="text-3xl font-bold mb-4">Cookie Policy</h1></div>`)));
app.get('/refund-policy', (req, res) => res.send(htmlWrapper('Refund Policy', `<div class="max-w-4xl mx-auto py-16 px-4"><h1 class="text-3xl font-bold mb-4">Refund Policy</h1></div>`)));

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

// Empty Openings Page (/p/:slug/booking)
app.get('/p/:slug/booking', (req, res) => {
    const profile = profiles.find(p => p.slug === req.params.slug) || profiles[0];

    res.send(`<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>No Openings - ${profile.name}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 font-sans antialiased flex flex-col justify-between p-6">
    <div class="max-w-md w-full mx-auto my-auto py-12 text-center" x-data="{ copied: false }">
        <div class="mb-6">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-sky-500 to-indigo-600 text-white font-black text-2xl flex items-center justify-center mx-auto shadow-md mb-3">
                <i class="fa-solid fa-calendar-xmark"></i>
            </div>
            <p class="text-sm font-bold text-slate-600">${profile.name}</p>
        </div>

        <div class="bg-white p-8 rounded-3xl border border-slate-200/80 shadow-xl">
            <div class="w-14 h-14 bg-sky-50 text-sky-600 rounded-2xl flex items-center justify-center font-bold text-xl mx-auto mb-4">
                <i class="fa-solid fa-clock font-normal"></i>
            </div>

            <h2 class="text-xl font-black text-slate-900 tracking-tight">No openings at the moment.</h2>

            <p class="text-sm text-slate-500 mt-2 max-w-xs mx-auto leading-relaxed">
                There are currently no available time slots or schedule openings. Please check again later.
            </p>

            <div class="mt-8 space-y-3">
                <a href="/p/${profile.slug}" class="w-full py-3.5 px-4 bg-sky-600 hover:bg-sky-700 text-white font-bold text-sm rounded-xl block shadow-md shadow-sky-500/20 transition">
                    <i class="fa-solid fa-arrow-left text-xs mr-2"></i> Back to Profile
                </a>

                <button @click="navigator.clipboard.writeText(window.location.origin + '/p/${profile.slug}'); copied = true; setTimeout(() => copied = false, 2000)" class="w-full py-3 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm rounded-xl block transition">
                    <i class="fa-solid fa-copy text-xs mr-2"></i>
                    <span x-text="copied ? 'Link Copied!' : 'Copy Profile Link'"></span>
                </button>
            </div>
        </div>
    </div>

    <footer class="text-center py-4 text-xs text-slate-400">
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
                    <a href="/p/john-doe" target="_blank" class="flex items-center gap-3 px-3 py-2.5 hover:bg-slate-800 text-slate-400 rounded-xl"><i class="fa-solid fa-id-card"></i> Ex 1: John Doe</a>
                    <a href="/p/sarah-sharma" target="_blank" class="flex items-center gap-3 px-3 py-2.5 hover:bg-slate-800 text-slate-400 rounded-xl"><i class="fa-solid fa-id-card"></i> Ex 2: Sarah Sharma</a>
                    <a href="/p/alex-verma" target="_blank" class="flex items-center gap-3 px-3 py-2.5 hover:bg-slate-800 text-slate-400 rounded-xl"><i class="fa-solid fa-id-card"></i> Ex 3: Alex Verma</a>
                </nav>
            </div>
            <a href="/" class="text-sm font-semibold text-rose-400"><i class="fa-solid fa-right-from-bracket mr-2"></i> Exit Server</a>
        </aside>

        <main class="flex-1 p-8 max-w-6xl">
            <div class="flex items-center justify-between mb-8">
                <h1 class="text-2xl font-bold">Dashboard Overview</h1>
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

            <h3 class="font-bold text-lg mb-4">System Example Profiles</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                ${profiles.map(p => `
                    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-12 h-12 rounded-full bg-sky-600 text-white font-bold flex items-center justify-center text-xl">
                                ${p.name.charAt(0)}
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900">${p.name}</h4>
                                <p class="text-xs text-slate-500">${p.designation}</p>
                            </div>
                        </div>
                        <a href="/p/${p.slug}" target="_blank" class="block text-center py-2 bg-sky-600 text-white text-xs font-bold rounded-xl mb-2">View Profile Card</a>
                        <a href="/api/qr/${p.slug}" download="qr-${p.slug}.png" class="block text-center py-2 bg-slate-900 text-white text-xs font-bold rounded-xl">Download QR PNG</a>
                    </div>
                `).join('')}
            </div>
        </main>
    </div>
    `));
});

app.get('/login', (req, res) => res.redirect('/dashboard'));
app.get('/register', (req, res) => res.redirect('/dashboard'));

app.listen(PORT, () => {
    console.log(`\n==================================================`);
    console.log(`  Dynamic QR Social Profile SaaS Web Server Active`);
    console.log(`  Access Application at: http://localhost:${PORT}`);
    console.log(`==================================================\n`);
});
