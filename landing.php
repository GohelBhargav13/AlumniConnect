<!DOCTYPE html>
<html lang="en" class="scroll-smooth antialiased">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>AlumniConnect – Bridge Alumni & Students</title>
  <meta name="description" content="AlumniConnect connects alumni and students for opportunities, mentorship, and career growth." />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet" />
  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          fontFamily: { sans: ['Inter', 'ui-sans-serif', 'system-ui'] },
          colors: {
            brand: {
              50:'#eef2ff',100:'#e0e7ff',200:'#c7d2fe',300:'#a5b4fc',400:'#818cf8',500:'#6366f1',600:'#4f46e5',700:'#4338ca',800:'#3730a3',900:'#312e81'
            },
            accent: {
              50:'#f0fdf4',100:'#dcfce7',200:'#bbf7d0',300:'#86efac',400:'#4ade80',500:'#22c55e',600:'#16a34a',700:'#15803d',800:'#166534',900:'#14532d'
            }
          },
          boxShadow: {
            'glow': '0 0 40px rgba(99,102,241,0.35)'
          },
          animation: {
            'fade-in-up':'fadeInUp 0.6s ease both'
          },
          keyframes: {
            fadeInUp: {
              '0%':{opacity:0,transform:'translateY(20px)'},
              '100%':{opacity:1,transform:'translateY(0)'}
            }
          }
        }
      }
    }
  </script>
  <style>
    :root { color-scheme: light dark; }
    html { scroll-padding-top: 5rem; }
  </style>
</head>
<body class="bg-white text-slate-800 dark:bg-slate-900 dark:text-slate-100 font-sans">
  <!-- Skip to content -->
  <a href="#main" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-50 bg-brand-600 text-white px-4 py-2 rounded-md">Skip to content</a>

  <!-- Top Banner (Optional Announcement) -->
  <div id="top-banner" class="hidden lg:block bg-brand-600 text-white text-center text-sm py-2">🎉 Join the Beta Version     – Alumni & Students sign up today!</div>

  <!-- Header -->
  <header class="sticky top-0 z-40 bg-white/80 dark:bg-slate-900/80 backdrop-blur border-b border-slate-200 dark:border-slate-700">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 flex h-16 items-center justify-between">
      <div class="flex items-center gap-2">
        <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-brand-600 text-white font-bold text-xl">AC</span>
        <span class="hidden sm:inline font-bold tracking-tight">AlumniConnect</span>
      </div>
      <nav aria-label="Primary" class="hidden md:flex md:items-center md:gap-8 text-sm font-medium">
        <a href="#how-it-works" class="hover:text-brand-600">How it Works</a>
        <a href="#features" class="hover:text-brand-600">Features</a>
        <a href="#testimonials" class="hover:text-brand-600">Success</a>    
      </nav>
      <div class="flex items-center gap-2">
        <button id="darkModeToggle" class="p-2 rounded-md border border-slate-300 dark:border-slate-600 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800" aria-pressed="false" aria-label="Toggle dark mode">
          <svg id="icon-sun" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5 hidden"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>
          <svg id="icon-moon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
        </button>
        <a href="./login.php" class="hidden sm:inline-block rounded-md border border-brand-600 px-3 py-1.5 text-brand-600 hover:bg-brand-50 dark:hover:bg-brand-600/20">Login</a>
        <a href="#cta-section" class="hidden sm:inline-block rounded-md bg-brand-600 px-3 py-1.5 text-white shadow hover:bg-brand-700">Get Started</a>
        <button id="mobileMenuBtn" class="md:hidden p-2 rounded-md border border-slate-300 dark:border-slate-600" aria-expanded="false" aria-controls="mobileMenu">
          <span class="sr-only">Open menu</span>
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" /></svg>
        </button>
      </div>
    </div>
    <!-- Mobile Menu -->
    <div id="mobileMenu" class="md:hidden hidden border-t border-slate-200 dark:border-slate-700">
      <nav aria-label="Mobile" class="px-4 py-4 flex flex-col gap-3 text-sm">
        <a href="#how-it-works" class="hover:text-brand-600">How it Works</a>
        <a href="#features" class="hover:text-brand-600">Features</a>
        <a href="#testimonials" class="hover:text-brand-600">Success Stories</a>
        <div class="pt-3 flex gap-2">
          <a href="/login" class="flex-1 rounded-md border border-brand-600 px-4 py-2 text-center text-brand-600 hover:bg-brand-50 dark:hover:bg-brand-600/20">Login</a>
          <a href="#cta-section" class="flex-1 rounded-md bg-brand-600 px-4 py-2 text-center text-white shadow hover:bg-brand-700">Get Started</a>
        </div>
      </nav>
    </div>
  </header>

  <main id="main" class="relative">
    <!-- Hero Split Audience -->
    <section id="hero" class="relative">
      <div class="grid grid-cols-1 lg:grid-cols-2 min-h-[75vh]">
        <!-- Alumni Panel -->
        <div class="relative group overflow-hidden bg-gradient-to-br from-brand-600 to-brand-800 text-white flex items-center justify-center p-12 sm:p-16">
          <div class="absolute inset-0 opacity-20 bg-[url('https://images.unsplash.com/photo-1529333166437-7750a6dd5a70?auto=format&fit=crop&w=1200&q=60')] bg-cover bg-center group-hover:opacity-30 transition-opacity"></div>
          <div class="relative max-w-md text-center space-y-6 animate-fade-in-up">
            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight">I'm an Alumni</h1>
            <p class="text-brand-50 text-base sm:text-lg">Share opportunities, mentor students, and stay connected with your campus community.</p>
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
              <a href="./alumni/alumni_register.php" class="rounded-md bg-white text-brand-700 px-6 py-3 font-semibold shadow hover:bg-brand-50">Join as Alumni</a>
              <a href="./login.php" class="rounded-md border border-white/60 px-6 py-3 font-semibold hover:bg-white/10">Alumni Login</a>
            </div>
          </div>
        </div>
        <!-- Student Panel -->
        <div class="relative group overflow-hidden bg-gradient-to-br from-accent-500 to-accent-700 text-white flex items-center justify-center p-12 sm:p-16">
          <div class="absolute inset-0 opacity-25 bg-[url('https://images.unsplash.com/photo-1529070538774-1843cb3265df?auto=format&fit=crop&w=1200&q=60')] bg-cover bg-center group-hover:opacity-35 transition-opacity"></div>
          <div class="relative max-w-md text-center space-y-6 animate-fade-in-up">
            <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight">I'm a Student</h2>
            <p class="text-accent-50 text-base sm:text-lg">Discover internships, get career guidance, and build meaningful alumni connections.</p>
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
              <a href="./student/student_register.php" class="rounded-md bg-white text-accent-700 px-6 py-3 font-semibold shadow hover:bg-accent-50">Join as Student</a>
              <a href="./login.php" class="rounded-md border border-white/60 px-6 py-3 font-semibold hover:bg-white/10">Student Login</a>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Social Proof / Stats -->
    <section id="stats" class="py-20 bg-slate-50 dark:bg-slate-800">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-2xl sm:text-3xl font-bold mb-10">Growing Community</h2>
        <dl class="grid grid-cols-2 sm:grid-cols-4 gap-8">
          <div class="flex flex-col">
            <dt class="text-sm text-slate-500 dark:text-slate-400">Alumni Engaged</dt>
            <dd class="text-3xl font-extrabold text-brand-600 dark:text-brand-400" data-count="4500">4.5K+</dd>
          </div>
          <div class="flex flex-col">
            <dt class="text-sm text-slate-500 dark:text-slate-400">Students Registered</dt>
            <dd class="text-3xl font-extrabold text-accent-600 dark:text-accent-400" data-count="22000">22K+</dd>
          </div>
          <div class="flex flex-col">
            <dt class="text-sm text-slate-500 dark:text-slate-400">Opportunities Posted</dt>
            <dd class="text-3xl font-extrabold text-brand-600 dark:text-brand-400" data-count="1200">1.2K+</dd>
          </div>
          <div class="flex flex-col">
            <dt class="text-sm text-slate-500 dark:text-slate-400">Mentorship Matches</dt>
            <dd class="text-3xl font-extrabold text-accent-600 dark:text-accent-400" data-count="980">980+</dd>
          </div>
        </dl>
      </div>
    </section>

    <!-- How It Works -->
    <section id="how-it-works" class="py-24">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mx-auto text-center mb-16">
          <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">How AlumniConnect Works</h2>
          <p class="mt-4 text-lg text-slate-600 dark:text-slate-300">Simple steps to create real outcomes—jobs, internships, mentoring, and lifelong connections.</p>
        </div>
        <ol class="grid gap-12 sm:grid-cols-3">
          <li class="relative flex flex-col items-center text-center">
            <span class="flex h-14 w-14 items-center justify-center rounded-full bg-brand-600 text-white text-xl font-bold shadow">1</span>
            <h3 class="mt-6 text-xl font-semibold">Create a Profile</h3>
            <p class="mt-2 text-slate-600 dark:text-slate-300">Alumni showcase experience; students highlight interests, skills & goals.</p>
          </li>
          <li class="relative flex flex-col items-center text-center">
            <span class="flex h-14 w-14 items-center justify-center rounded-full bg-brand-600 text-white text-xl font-bold shadow">2</span>
            <h3 class="mt-6 text-xl font-semibold">Post or Apply</h3>
            <p class="mt-2 text-slate-600 dark:text-slate-300">Alumni post opportunities or mentorship slots. Students browse & apply.</p>
          </li>
          <li class="relative flex flex-col items-center text-center">
            <span class="flex h-14 w-14 items-center justify-center rounded-full bg-brand-600 text-white text-xl font-bold shadow">3</span>
            <h3 class="mt-6 text-xl font-semibold">Connect & Grow</h3>
            <p class="mt-2 text-slate-600 dark:text-slate-300">Messaging, scheduling & feedback tools help both sides collaborate.</p>
          </li>
        </ol>
      </div>
    </section>

    <!-- Feature Highlights -->
    <section id="features" class="py-24 bg-slate-50 dark:bg-slate-800">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto text-center mb-16">
          <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">Everything You Need to Build Lifelong Connections</h2>
          <p class="mt-4 text-lg text-slate-600 dark:text-slate-300">Tools for alumni engagement, student success, and career-ready mentorship.</p>
        </div>
        <div class="grid gap-10 sm:grid-cols-2 lg:grid-cols-3">
          <!-- Feature Card -->
          <div class="p-8 rounded-2xl bg-white dark:bg-slate-900 shadow hover:shadow-glow transition-shadow">
            <div class="h-10 w-10 rounded-full bg-brand-100 dark:bg-brand-600/20 flex items-center justify-center text-brand-600 dark:text-brand-400 mb-4">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5"><path d="M7 10v12"/><path d="M15 5v17"/><path d="M11 2v20"/></svg>
            </div>
            <h3 class="font-semibold text-lg mb-2">Opportunity Board</h3>
            <p class="text-slate-600 dark:text-slate-300 text-sm">Post jobs, internships, projects, events & campus visits with rich details & deadlines.</p>
          </div>
          <!-- Feature Card -->
          <div class="p-8 rounded-2xl bg-white dark:bg-slate-900 shadow hover:shadow-glow transition-shadow">
            <div class="h-10 w-10 rounded-full bg-brand-100 dark:bg-brand-600/20 flex items-center justify-center text-brand-600 dark:text-brand-400 mb-4">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
            </div>
            <h3 class="font-semibold text-lg mb-2">Student Applications</h3>
            <p class="text-slate-600 dark:text-slate-300 text-sm">Students apply with one click; alumni review profiles, resumes & messages.</p>
          </div>
          <!-- Feature Card -->
          <div class="p-8 rounded-2xl bg-white dark:bg-slate-900 shadow hover:shadow-glow transition-shadow">
            <div class="h-10 w-10 rounded-full bg-brand-100 dark:bg-brand-600/20 flex items-center justify-center text-brand-600 dark:text-brand-400 mb-4">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16h6M21 16V8a2 2 0 00-2-2h-4l-2-2H9L7 6H3a2 2 0 00-2 2v8" /></svg>
            </div>
            <h3 class="font-semibold text-lg mb-2">Connection Requests</h3>
            <p class="text-slate-600 dark:text-slate-300 text-sm">Students request mentorship; alumni accept & start private chats.</p>
          </div>
          <!-- Feature Card -->
          <div class="p-8 rounded-2xl bg-white dark:bg-slate-900 shadow hover:shadow-glow transition-shadow">
            <div class="h-10 w-10 rounded-full bg-brand-100 dark:bg-brand-600/20 flex items-center justify-center text-brand-600 dark:text-brand-400 mb-4">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5V4H2v16h5"/><rect width="10" height="12" x="7" y="8" rx="2" ry="2"/></svg>
            </div>
            <h3 class="font-semibold text-lg mb-2">In-App Messaging</h3>
            <p class="text-slate-600 dark:text-slate-300 text-sm">Secure message threads with attachments, meeting links & follow-up reminders.</p>
          </div>
          <!-- Feature Card -->
          <div class="p-8 rounded-2xl bg-white dark:bg-slate-900 shadow hover:shadow-glow transition-shadow">
            <div class="h-10 w-10 rounded-full bg-brand-100 dark:bg-brand-600/20 flex items-center justify-center text-brand-600 dark:text-brand-400 mb-4">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2l4-4"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/></svg>
            </div>
            <h3 class="font-semibold text-lg mb-2">Verified Users</h3>
            <p class="text-slate-600 dark:text-slate-300 text-sm">Institution-linked email or admin approval keeps the community trusted.</p>
          </div>
          <!-- Feature Card -->
          <div class="p-8 rounded-2xl bg-white dark:bg-slate-900 shadow hover:shadow-glow transition-shadow">
            <div class="h-10 w-10 rounded-full bg-brand-100 dark:bg-brand-600/20 flex items-center justify-center text-brand-600 dark:text-brand-400 mb-4">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3v18h18"/><path stroke-linecap="round" stroke-linejoin="round" d="M7 13l3 3l7-7"/></svg>
            </div>
            <h3 class="font-semibold text-lg mb-2">Analytics & Engagement</h3>
            <p class="text-slate-600 dark:text-slate-300 text-sm">Track signups, post views, applications & mentorship activity with admin dashboards.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Featured Opportunities (sample cards) -->

    <!-- Testimonials / Success Stories -->
    <section id="testimonials" class="py-24 bg-slate-50 dark:bg-slate-800">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mx-auto text-center mb-16">
          <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">Real Outcomes from Real Connections</h2>
          <p class="mt-4 text-lg text-slate-600 dark:text-slate-300">Students land roles. Alumni give back. Communities grow stronger.</p>
        </div>
        <div class="grid gap-10 sm:grid-cols-2 lg:grid-cols-3">
          <!-- Testimonial Card -->
          <figure class="p-8 rounded-2xl bg-white dark:bg-slate-900 shadow border border-slate-200 dark:border-slate-700">
            <blockquote class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed">“I posted a small React internship and got 50+ qualified applicants within a week. We hired two, both now full time!”</blockquote>
            <figcaption class="mt-6 flex items-center gap-4">
              <img src="https://i.pravatar.cc/64?img=12" alt="Priya Shah" class="h-12 w-12 rounded-full ring-2 ring-brand-600" />
              <div>
                <div class="font-semibold">Priya Shah</div>
                <div class="text-xs text-slate-500 dark:text-slate-400">Founder, StartupX · B.Tech '21</div>
              </div>
            </figcaption>
          </figure>
          <!-- Testimonial Card -->
          <figure class="p-8 rounded-2xl bg-white dark:bg-slate-900 shadow border border-slate-200 dark:border-slate-700">
            <blockquote class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed">“Connecting with alumni helped me understand real-world security roles. My mentor guided my resume and I got my first SOC internship.”</blockquote>
            <figcaption class="mt-6 flex items-center gap-4">
              <img src="https://i.pravatar.cc/64?img=24" alt="Aman Gupta" class="h-12 w-12 rounded-full ring-2 ring-brand-600" />
              <div>
                <div class="font-semibold">Aman Gupta</div>
                <div class="text-xs text-slate-500 dark:text-slate-400">3rd Yr CSE Student</div>
              </div>
            </figcaption>
          </figure>
          <!-- Testimonial Card -->
          <figure class="p-8 rounded-2xl bg-white dark:bg-slate-900 shadow border border-slate-200 dark:border-slate-700">
            <blockquote class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed">“Admin analytics show which departments are most active. It helped us plan targeted outreach.”</blockquote>
            <figcaption class="mt-6 flex items-center gap-4">
              <img src="https://i.pravatar.cc/64?img=52" alt="Faculty Coordinator" class="h-12 w-12 rounded-full ring-2 ring-brand-600" />
              <div>
                <div class="font-semibold">Prof. K. Iyer</div>
                <div class="text-xs text-slate-500 dark:text-slate-400">Faculty Alumni Coordinator</div>
              </div>
            </figcaption>
          </figure>
        </div>
      </div>
    </section>

    <!-- Call to Action -->
    <section id="cta-section" class="py-32 relative overflow-hidden">
      <div class="absolute inset-0 -z-10 bg-gradient-to-r from-brand-600 via-brand-700 to-brand-800"></div>
      <div class="relative mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 text-center text-white">
        <h2 class="text-4xl font-extrabold tracking-tight">Ready to Get Started?</h2>
        <p class="mt-4 text-lg text-brand-100">Join thousands of alumni & students building meaningful career connections.</p>
        <div class="mt-8 flex flex-col sm:flex-row sm:justify-center gap-4">
          <a href="./alumni/alumni_register.php" class="rounded-md bg-white text-brand-700 px-8 py-4 font-semibold shadow hover:bg-brand-50">Join as Alumni</a>
          <a href="./student/student_register.php" class="rounded-md bg-brand-900/40 ring-1 ring-inset ring-white/40 px-8 py-4 font-semibold hover:bg-brand-900/60">Join as Student</a>
        </div>
      </div>
    </section>

    <!-- FAQ -->
  </main>

  <!-- Footer -->
  <footer class="bg-slate-900 text-slate-300">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16 grid gap-10 sm:grid-cols-2 lg:grid-cols-4 text-sm">
      <div>
        <div class="flex items-center gap-2 mb-4">
          <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-brand-600 text-white font-bold text-xl">AC</span>
          <span class="font-bold">AlumniConnect</span>
        </div>
        <p class="text-slate-400 max-w-xs">Building meaningful bridges between alumni & students for career growth and lifelong community.</p>
      </div>
      <div>
        <h4 class="font-semibold mb-3 text-white">Platform</h4>
        <ul class="space-y-2">
          <li><a class="hover:text-white" href="#features">Features</a></li>
          <li><a class="hover:text-white" href="#opportunities">Opportunities</a></li>
          <li><a class="hover:text-white" href="#testimonials">Success Stories</a></li>
          <li><a class="hover:text-white" href="#faq">FAQ</a></li>
        </ul>
      </div>
      <div>
        <h4 class="font-semibold mb-3 text-white">For Users</h4>
        <ul class="space-y-2">
          <li><a class="hover:text-white" href="./alumni/alumni_register.php">Alumni Signup</a></li>
          <li><a class="hover:text-white" href="./student/student_register.php">Student Signup</a></li>
          <li><a class="hover:text-white" href="./login.php">Login</a></li>
          <li><a class="hover:text-white" href="/support">Support</a></li>
        </ul>
      </div>
      <div>
      </div>
    </div>
    <div class="border-t border-slate-700 py-6 text-center text-xs text-slate-500">© <span id="year"></span> AlumniConnect. All rights reserved.</div>
  </footer>

  <!-- Scripts -->
  <script>
    // Dark Mode Toggle
    const darkToggle = document.getElementById('darkModeToggle');
    const iconSun = document.getElementById('icon-sun');
    const iconMoon = document.getElementById('icon-moon');
    const storedTheme = localStorage.getItem('ac-theme');

    function setTheme(mode){
      if(mode==='dark'){
        document.documentElement.classList.add('dark');
        iconSun.classList.remove('hidden');
        iconMoon.classList.add('hidden');
        darkToggle.setAttribute('aria-pressed','true');
      }else{
        document.documentElement.classList.remove('dark');
        iconSun.classList.add('hidden');
        iconMoon.classList.remove('hidden');
        darkToggle.setAttribute('aria-pressed','false');
      }
      localStorage.setItem('ac-theme',mode);
    }

    // Initialize
    if(storedTheme){
      setTheme(storedTheme);
    }else{
      setTheme(prefersDark?'dark':'light');
    }

    darkToggle.addEventListener('click',()=>{
      const isDark = document.documentElement.classList.contains('dark');
      setTheme(isDark?'light':'dark');
    });

    // Mobile Menu Toggle
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const mobileMenu = document.getElementById('mobileMenu');
    mobileMenuBtn.addEventListener('click',()=>{
      const expanded = mobileMenuBtn.getAttribute('aria-expanded')==='true';
      mobileMenuBtn.setAttribute('aria-expanded', String(!expanded));
      mobileMenu.classList.toggle('hidden');
    });

    // Current Year in Footer
    document.getElementById('year').textContent = new Date().getFullYear();

    // Optional animated counters (basic)
    // document.querySelectorAll('[data-count]').forEach(el=>{
    //   const target = Number(el.dataset.count);
    //   let cur = 0;
    //   const step = Math.ceil(target/60);
    //   const timer = setInterval(()=>{
    //     cur += step;
    //     if(cur >= target){
    //       cur = target;
    //       clearInterval(timer);
    //     }
    //     el.textContent = cur.toLocaleString();
    //   },16);
    // });
  </script>
</body>
</html>