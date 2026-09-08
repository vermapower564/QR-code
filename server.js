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

// In-Memory Database for Live Preview
let profiles = [
    {
        id: 1,
        slug: 'john-doe',
        name: 'John Doe',
        designation: 'CEO & Co-founder',
        company: 'ABC Technologies',
        bio: 'Technology entrepreneur building next-gen web & cloud solutions.',
        phone: '+1 999 999 9999',
        email: 'john@example.com',
        website: 'https://example.com',
        status: 'active',
        theme_data: {
            bg_color: '#f8fafc',
            text_color: '#0f172a',
            button_style: 'rounded-xl',
            theme_preset: 'Classic'
        },
        scans_count: 1240,
        social_links: [
            { id: 101, platform: 'instagram', title: 'Instagram Portfolio', url: 'https://instagram.com/example' },
            { id: 102, platform: 'linkedin', title: 'LinkedIn Profile', url: 'https://linkedin.com/in/example' },
            { id: 103, platform: 'youtube', title: 'YouTube Channel', url: 'https://youtube.com/c/example' }
        ],
        custom_links: [
            { id: 201, title: 'Book a Meeting', description: 'Schedule 30-min strategy call on Calendly', url: 'https://calendly.com/example' }
        ]
    }
];

let scans = [
    { id: 1, profile_id: 1, scanned_at: new Date(), ip_hash: 'hash_123', device: 'Mobile', browser: 'Chrome' }
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
    ${content}
</body>
</html>`;
}

// 1. Landing Page (/)
app.get('/', (req, res) => {
    res.send(htmlWrapper('Dynamic QR Social Profile SaaS', `
    <header class="sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 h-16 flex items-center justify-between">
            <a href="/" class="flex items-center gap-2 text-xl font-bold text-slate-900">
                <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-sky-500 to-indigo-600 flex items-center justify-center text-white font-black text-sm">
                    <i class="fa-solid fa-qrcode"></i>
                </div>
                <span>QR Identity</span>
            </a>
            <div class="flex items-center gap-4">
                <a href="/login" class="text-sm font-semibold text-slate-700 hover:text-slate-900">Sign in</a>
                <a href="/dashboard" class="px-4 py-2 text-sm font-semibold text-white bg-sky-600 rounded-xl hover:bg-sky-700 shadow-sm">
                    Open Dashboard
                </a>
            </div>
        </div>
    </header>

    <section class="py-20 text-center bg-gradient-to-b from-sky-50 to-white px-4">
        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-sky-100 text-sky-700 text-xs font-semibold uppercase mb-6">
            <i class="fa-solid fa-bolt"></i> Dynamic QR Code & Profile Server Running
        </div>
        <h1 class="text-4xl sm:text-6xl font-black tracking-tight max-w-4xl mx-auto leading-tight text-slate-900">
            Create your digital identity. <br/>
            <span class="bg-gradient-to-r from-sky-600 to-indigo-600 bg-clip-text text-transparent">One QR code</span> for all your links.
        </h1>
        <p class="mt-6 text-lg text-slate-600 max-w-2xl mx-auto">
            Connect your website, Instagram, LinkedIn, WhatsApp, and contact card under a single dynamic QR code.
        </p>

        <div class="mt-10 flex flex-wrap justify-center gap-4">
            <a href="/dashboard" class="px-8 py-4 bg-sky-600 text-white font-bold rounded-2xl shadow-lg hover:bg-sky-700 transition">
                Create Your QR Profile <i class="fa-solid fa-arrow-right ml-2 text-sm"></i>
            </a>
            <a href="/p/john-doe" target="_blank" class="px-8 py-4 bg-slate-100 text-slate-800 font-bold rounded-2xl hover:bg-slate-200 transition">
                View Live Demo Profile
            </a>
        </div>
    </section>
    `));
});

// 2. Dynamic QR Code API Endpoint
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

// 3. Public Profile (/p/:slug)
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

    scans.push({ id: scans.length + 1, profile_id: profile.id, scanned_at: new Date(), ip_hash: 'demo_hash', device: 'Mobile', browser: 'Chrome' });
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

        <!-- Action Buttons -->
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

        <!-- Social Links -->
        <div class="space-y-3 mb-8">
            <h3 class="text-xs uppercase font-bold tracking-widest opacity-60 px-1">Social Networks</h3>
            ${profile.social_links.map(l => `
                <a href="${l.url}" target="_blank" class="w-full p-4 bg-white border border-slate-200 rounded-2xl flex items-center justify-between font-bold text-sm text-slate-900 shadow-sm hover:translate-y-[-2px] transition">
                    <div class="flex items-center gap-3">
                        <i class="fa-brands fa-${l.platform} text-xl text-sky-600"></i>
                        <span>${l.title}</span>
                    </div>
                    <i class="fa-solid fa-chevron-right text-xs opacity-40"></i>
                </a>
            `).join('')}
        </div>

        <!-- Custom Links -->
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

// 4. Contact Card (.vcf Download)
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

// 5. Dashboard Overview (/dashboard)
app.get('/dashboard', (req, res) => {
    const profile = profiles[0];
    res.send(htmlWrapper('Dashboard', `
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
                    <a href="/p/john-doe" target="_blank" class="flex items-center gap-3 px-3 py-2.5 hover:bg-slate-800 text-slate-400 rounded-xl"><i class="fa-solid fa-id-card"></i> Preview Profile</a>
                </nav>
            </div>
            <a href="/" class="text-sm font-semibold text-rose-400"><i class="fa-solid fa-right-from-bracket mr-2"></i> Exit Server</a>
        </aside>

        <main class="flex-1 p-8 max-w-6xl">
            <div class="flex items-center justify-between mb-8">
                <h1 class="text-2xl font-bold">Dashboard Overview</h1>
                <a href="/p/${profile.slug}" target="_blank" class="px-4 py-2 bg-sky-600 text-white rounded-xl font-bold text-sm shadow">View Public Profile</a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                    <span class="text-xs font-bold text-slate-500 uppercase">Profiles</span>
                    <p class="text-3xl font-black mt-2">${profiles.length}</p>
                </div>
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                    <span class="text-xs font-bold text-slate-500 uppercase">Total Scans</span>
                    <p class="text-3xl font-black mt-2">${profile.scans_count}</p>
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

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-slate-200">
                    <h3 class="font-bold text-lg mb-4">Active Profile Card</h3>
                    <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-sky-600 text-white font-bold flex items-center justify-center text-xl">
                                ${profile.name.charAt(0)}
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900">${profile.name}</h4>
                                <a href="/p/${profile.slug}" target="_blank" class="text-xs font-mono text-sky-600 hover:underline">/p/${profile.slug}</a>
                            </div>
                        </div>
                        <a href="/api/qr/${profile.slug}" download="qr-${profile.slug}.png" class="px-4 py-2 bg-slate-900 text-white text-xs font-bold rounded-xl">Download PNG</a>
                    </div>
                </div>

                <div class="bg-slate-900 text-white p-6 rounded-2xl text-center">
                    <h3 class="font-bold text-sm text-slate-300 mb-4">DYNAMIC QR PREVIEW</h3>
                    <img src="/api/qr/${profile.slug}" alt="QR" class="w-48 h-48 mx-auto bg-white p-2 rounded-xl mb-4 shadow"/>
                    <p class="font-bold">${profile.name}</p>
                    <p class="text-xs text-sky-400 font-mono mt-1">/p/${profile.slug}</p>
                </div>
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
