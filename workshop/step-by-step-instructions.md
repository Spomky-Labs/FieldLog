# PWA Workshop - Step-by-Step Instructions for Facilitators

This document provides detailed instructions for each workshop section, including exactly what to say, show, and do at each step.

---

## Section 1: Introduction & Setup (30 minutes)

### Preparation (Before Participants Arrive)
- [ ] Write WiFi credentials on board
- [ ] Test HTTPS access on your device
- [ ] Have browser DevTools open on projection
- [ ] Prepare demo of a working PWA (FieldLog or other)

### Step 1.1: Welcome & Introductions (5 minutes)

**What to Say:**
> "Welcome to the PWA Workshop! Today we're going to transform FieldLog, a web application, into a Progressive Web App that works offline, can be installed like a native app, and integrates deeply with the operating system."

**Action:**
- Quick round of introductions if group is small (< 15 people)
- Or: Ask who has used a PWA before

### Step 1.2: Demo a Working PWA (5 minutes)

**What to Show:**
1. Open a PWA in browser (Twitter/X, Starbucks, or completed FieldLog)
2. Show install prompt and click install
3. Show installed app in app drawer/start menu
4. Open the installed app - point out:
   - No browser chrome
   - Appears in app switcher
   - Has its own icon
5. Turn on Airplane mode
6. Show the app still works offline

**What to Say:**
> "Notice how this looks and feels like a native app, but it's actually just a website with superpowers. That's what we're building today."

### Step 1.3: PWA Concepts Overview (10 minutes)

**What to Explain:**

**Slide 1: What is a PWA?**
> "PWA = Progressive Web App. It's a regular website that uses modern web APIs to deliver an app-like experience."

**Key Points:**
- Progressive: Works for everyone, enhanced for capable browsers
- Responsive: Fits any device
- Connectivity-independent: Works offline or on low-quality networks
- App-like: Feels like an app
- Fresh: Always up-to-date thanks to Service Worker
- Safe: HTTPS only
- Discoverable: Search engines can find it
- Installable: Can be added to home screen
- Linkable: Can be shared via URL

**Slide 2: Requirements for PWA**
> "Three things are absolutely required for a PWA:"

1. **HTTPS** - Security is mandatory (localhost excepted for development)
2. **Web App Manifest** - JSON file describing your app
3. **Service Worker** - JavaScript that runs in background

**Slide 3: Benefits**
- One codebase for all platforms
- No app store approval process (though you can still publish to stores)
- Instant updates - no user action needed
- Smaller than native apps
- Users can try before "installing"
- Web development skills - no need to learn Swift/Kotlin

**Whiteboard Draw:**
```
    ┌─────────────┐
    │   Browser   │
    └──────┬──────┘
           │
    ┌──────▼──────┐
    │  Your App   │
    └──────┬──────┘
           │
    ┌──────▼──────────┐
    │ Service Worker  │ ← Sits between app and network
    └──────┬──────────┘
           │
    ┌──────▼──────┐
    │   Network   │
    └─────────────┘
```

### Step 1.4: Bundle Installation (10 minutes)

**What to Say:**
> "We're using the Spomky-Labs PWA Bundle for Symfony. This bundle handles a lot of the boilerplate for us - generating the manifest, configuring the Service Worker, and integrating with Symfony's architecture."

**Guide Participants:**

1. **Install the bundle:**
```bash
composer require spomky-labs/pwa-bundle
```

**Walk around and help** with any installation issues.

2. **Check the bundle is registered:**
```bash
cat config/bundles.php | grep PWA
```

Should see:
```php
SpomkyLabs\PwaBundle\SpomkyLabsPwaBundle::class => ['all' => true],
```

3. **Clear cache:**
```bash
castor console cache:clear
```

4. **Check routes:**
```bash
castor console debug:router | grep pwa
```

Should see routes for manifest and service worker.

**Checkpoint:**
> "Everyone should now have the bundle installed. Raise your hand if you're stuck."

**Wrap Up Section 1:**
> "Great! We have the bundle installed. Next, we'll create our Web App Manifest to make FieldLog installable."

---

## Section 2: Web App Manifest (1 hour)

### Preparation
- [ ] Have manifest.json example open
- [ ] Have icon files ready to show
- [ ] Have maskable.app open in browser tab
- [ ] Have test devices ready

### Step 2.1: Manifest Overview (5 minutes)

**What to Say:**
> "The Web App Manifest is a JSON file that tells the browser how your app should behave when installed. It's like a metadata file for your PWA."

**Show Example manifest.json:**
```json
{
  "name": "FieldLog - Personal Journal",
  "short_name": "FieldLog",
  "description": "Your personal multimedia journaling application",
  "start_url": "/",
  "display": "standalone",
  "background_color": "#ffffff",
  "theme_color": "#4f46e5",
  "icons": [
    {
      "src": "/icons/icon-192.png",
      "sizes": "192x192",
      "type": "image/png"
    }
  ]
}
```

**Explain Each Field:**
- `name`: Full app name (shown on install prompt)
- `short_name`: Short name for home screen (12 chars max recommended)
- `description`: Describes what the app does
- `start_url`: URL to load when app launches
- `display`: How the app appears (standalone = no browser UI)
- `background_color`: Splash screen color
- `theme_color`: Browser theme color
- `icons`: App icons in various sizes

### Step 2.2: Configure the Bundle (15 minutes)

**What to Say:**
> "The bundle will generate the manifest for us. We just need to configure it. Let's create the configuration file."

**Guide Participants:**

1. **Create configuration file:**
```bash
cat > config/packages/pwa.yaml << 'EOF'
pwa:
    manifest:
        name: 'FieldLog - Personal Journal'
        short_name: 'FieldLog'
        description: 'Your personal multimedia journaling application'
        theme_color: '#4f46e5'
        background_color: '#ffffff'
        display: 'standalone'
        start_url: '/'
        icons:
            - src: '/icons/icon-192.png'
              sizes: '192x192'
              type: 'image/png'
            - src: '/icons/icon-512.png'
              sizes: '512x512'
              type: 'image/png'
            - src: '/icons/icon-maskable.png'
              sizes: '512x512'
              type: 'image/png'
              purpose: 'maskable'
EOF
```

2. **Clear cache:**
```bash
castor console cache:clear
```

3. **Check the manifest is generated:**
```bash
curl -k https://localhost/manifest.json
```

**Troubleshooting:**
- If manifest doesn't exist, check bundle is configured correctly
- If cache clear doesn't work, restart Docker: `castor restart`

### Step 2.3: Create App Icons (20 minutes)

**What to Say:**
> "PWAs need icons in multiple sizes. At minimum, you need 192x192 and 512x512. We'll also create a maskable icon for Android."

**Option 1: Provide Pre-made Icons**
- Share a folder with icons already prepared
- Participants just copy to `public/icons/`

**Option 2: Guide Icon Creation**

1. **Show icon generation tool:**
   - Open https://realfavicongenerator.net/ or
   - Open https://www.pwabuilder.com/imageGenerator

2. **Guide participants:**
   - Upload a base image (FieldLog logo or simple design)
   - Download generated icons
   - Copy to `public/icons/` directory

3. **Maskable Icons:**
   - Open https://maskable.app/editor
   - Upload icon
   - Adjust safe zone
   - Download maskable version

**What to Say:**
> "Maskable icons are special for Android. They allow the system to apply any shape mask (circle, squircle, etc.) and your icon still looks good. Keep important parts in the safe zone."

**Guide Participants:**
```bash
# Create icons directory
mkdir -p public/icons

# Copy or download icons there
# Verify they exist
ls -lh public/icons/
```

### Step 2.4: Test Installation (15 minutes)

**What to Say:**
> "Now let's test if our app is installable. Chrome will show an install button when the PWA criteria are met."

**Demo on Projection:**

1. **Open Chrome DevTools** (F12)
2. **Go to Application tab** → Manifest
3. **Show manifest loaded correctly**
4. **Point out any errors**

**Check Installation Criteria:**
- [ ] Manifest with icons
- [ ] HTTPS or localhost
- [ ] Service Worker (we'll add this next, but show the requirement)
- [ ] Fetch event in Service Worker
- [ ] Engagement heuristics met (for automatic prompt)

**Show Install Manually:**
1. Chrome menu → Install FieldLog
2. Or: Click install icon in address bar
3. Show installed app opening

**Guide Participants:**
```bash
# Make sure app is running
castor start

# Open in browser
open https://localhost
```

Have them:
- Open DevTools → Application → Manifest
- Verify manifest loads
- Check for errors
- Try installing (might not work yet without Service Worker)

**On Mobile (Demo):**
1. Open on Android device
2. Chrome menu → "Add to Home Screen" or "Install app"
3. Show app icon on home screen
4. Open installed app

**Walk Around:**
- Help with manifest errors
- Help with icon issues
- Verify everyone sees their manifest in DevTools

### Step 2.5: Customize Display Mode (5 minutes)

**What to Say:**
> "The display mode controls how much browser UI is visible. Let's see the options."

**Show Different Display Modes:**

Edit `config/packages/pwa.yaml`:
```yaml
display: 'standalone'  # No browser UI (recommended)
# display: 'fullscreen'  # Completely immersive
# display: 'minimal-ui'  # Minimal browser controls
# display: 'browser'     # Normal browser window
```

**Test different modes:**
- Change config
- Clear cache
- Uninstall app
- Reinstall
- Show difference

**Recommendation:**
> "For FieldLog, 'standalone' is perfect. It feels like an app but users can still navigate intuitively."

**Checkpoint:**
> "Everyone should now have a working manifest and be able to see it in DevTools. We can't install yet because we need a Service Worker first. That's next!"

---

## Section 3: Service Worker Basics (1.5 hours)

### Preparation
- [ ] Have Service Worker lifecycle diagram ready
- [ ] Have DevTools Application tab ready to demo
- [ ] Prepare simple SW code examples
- [ ] Have network throttling ready to demo

### Step 3.1: Service Worker Concept (10 minutes)

**What to Say:**
> "A Service Worker is a JavaScript file that runs in the background, separate from your web page. It acts as a proxy between your app and the network, letting you intercept network requests and control caching."

**Whiteboard Draw:**
```
User's Browser
┌────────────────────────────────────┐
│                                    │
│  ┌──────────────┐                 │
│  │   Web Page   │                 │
│  │  (Your App)  │                 │
│  └───────┬──────┘                 │
│          │                         │
│          │ requests                │
│          ▼                         │
│  ┌──────────────────┐             │
│  │ Service Worker   │◄────────┐   │
│  │ (sw.js)          │         │   │
│  └────────┬─────────┘         │   │
│           │                   │   │
│           │ intercepts        │   │
│           │                   │   │
│           ▼                   │   │
│      ┌────────┐          ┌────┴───┐│
│      │ Cache  │          │Network ││
│      │Storage │          │        ││
│      └────────┘          └────────┘│
└────────────────────────────────────┘
```

**Key Points:**
- Runs in separate thread (doesn't block main page)
- No DOM access (can't directly manipulate the page)
- Event-driven (responds to events: install, activate, fetch)
- Requires HTTPS (security)
- Scoped to directory (default: where SW file is located)

**Lifecycle Events:**
```
install → activate → fetch (repeating)
     ↓        ↓
  (cache) (cleanup)
```

### Step 3.2: Service Worker Lifecycle Deep Dive (15 minutes)

**What to Say:**
> "Understanding the lifecycle is crucial. Let's walk through what happens when a user first visits your PWA."

**Draw Timeline on Whiteboard:**

```
First Visit:
1. Browser downloads sw.js
2. 'install' event fires → precache assets
3. SW sits in 'waiting' state
4. User closes all tabs
5. Next visit: 'activate' event fires → cleanup old caches
6. 'fetch' events start → intercept all network requests

Updates:
1. Browser detects new sw.js (even 1 byte change)
2. New SW installs alongside old SW
3. New SW waits until all tabs close
4. New SW activates and takes over
```

**Demo in DevTools:**

1. Open Application tab → Service Workers
2. Show current SW status
3. Check "Update on reload" (for development)
4. Show how to unregister
5. Show "Skip waiting" for forced updates

**Show Code Structure:**
```javascript
// sw.js basic structure
self.addEventListener('install', event => {
  console.log('SW installing...');
  // Cache assets here
});

self.addEventListener('activate', event => {
  console.log('SW activating...');
  // Clean up old caches here
});

self.addEventListener('fetch', event => {
  console.log('SW fetching:', event.request.url);
  // Intercept and handle requests here
});
```

### Step 3.3: Configure Service Worker with Bundle (20 minutes)

**What to Say:**
> "The bundle will generate our Service Worker. We configure caching strategies in pwa.yaml."

**Guide Participants:**

1. **Add Service Worker configuration to `config/packages/pwa.yaml`:**

```yaml
pwa:
    manifest:
        # ... existing manifest config ...

    serviceworker:
        enabled: true
        src: '/sw.js'
        scope: '/'
        use_cache: true

        # Precache - these are cached on SW install
        precache:
            - '/assets/app.css'
            - '/assets/app.js'
            - '/'  # Cache homepage

        # Workbox configuration
        workbox:
            enabled: true
            # Cache images
            - match: '/uploads/*'
              strategy: 'CacheFirst'
              cache_name: 'images'
              max_entries: 50
              max_age: 2592000  # 30 days

            # Cache API calls
            - match: '/api/*'
              strategy: 'NetworkFirst'
              cache_name: 'api'
              max_entries: 20
              max_age: 3600  # 1 hour
```

**Explain Strategies:**

**CacheFirst (Cache Falling Back to Network):**
> "Check cache first. If found, return it immediately. If not, fetch from network and cache for next time. Great for static assets that don't change often."

```
Request → Cache → Found? → Return
                     ↓ Not found
                  Network → Cache → Return
```

**NetworkFirst (Network Falling Back to Cache):**
> "Try network first. If successful, update cache. If network fails, return cached version. Great for dynamic content where you want the freshest data but need offline fallback."

```
Request → Network → Success? → Cache → Return
                       ↓ Failed
                     Cache → Found? → Return
                               ↓ Not found
                             Fail
```

**StaleWhileRevalidate:**
> "Return cached version immediately, but fetch from network in background to update cache. Great for content that changes but stale is acceptable."

```
Request → Cache → Return (immediately)
           ↓
        Network → Update cache (in background)
```

2. **Clear cache and restart:**
```bash
castor console cache:clear
castor restart
```

3. **Verify Service Worker is registered:**
```bash
curl -k https://localhost/sw.js
```

### Step 3.4: Test Service Worker (20 minutes)

**Demo Process:**

1. **Open DevTools → Application → Service Workers**
2. **Show SW registered and active**
3. **Go to Cache Storage**
4. **Show precached assets**

**Test Caching:**

1. **Load homepage** - Open Network tab
2. **Reload page** - Some assets from SW
3. **Check "Disable cache" in Network tab**
4. **Reload** - Still loads (from SW cache!)

**Test Network Strategies:**

**For CacheFirst (images):**
```bash
# Load a page with images
# Check Network tab - images from network (first time)
# Reload
# Check Network tab - images from Service Worker
```

**For NetworkFirst (API):**
```bash
# Make API request
# Go offline (DevTools throttling)
# Make same request
# Should work from cache
```

**Guide Participants:**

```bash
# Make sure app is running
castor start

# Open browser
open https://localhost

# Open DevTools (F12)
# Go to Application tab
```

Have them:
- [ ] Verify Service Worker is registered and activated
- [ ] Check Cache Storage shows precached assets
- [ ] Test network requests come from SW
- [ ] Try offline mode (Chrome: DevTools → Network → Throttling → Offline)

**Walk Around:**
- Help debug SW not registering
- Help identify caching issues
- Show how to read SW logs

### Step 3.5: Debugging Service Workers (15 minutes)

**What to Say:**
> "Service Workers can be tricky to debug. Let me show you the tools and techniques."

**DevTools Tips:**

1. **Application Tab → Service Workers**
   - See all registered SWs
   - Unregister
   - Update
   - See SW status
   - Start/Stop

2. **Console**
   - Filter by "Service Worker"
   - See install/activate/fetch logs

3. **Network Tab**
   - Filter shows "from ServiceWorker"
   - Shows what SW served vs network

4. **Cache Storage**
   - Inspect all caches
   - See what's cached
   - Delete items
   - Clear all

**Common Issues & Solutions:**

**Issue**: SW not updating
```
Solution:
- Check "Update on reload"
- Or: Unregister and reload
- Or: Hard refresh (Ctrl+Shift+R)
```

**Issue**: Wrong content cached
```
Solution:
- Clear cache storage
- Unregister SW
- Reload
```

**Issue**: SW not intercepting requests
```
Solution:
- Check SW scope
- Check fetch event handler exists
- Check SW is activated (not waiting)
```

**Demo Common Mistakes:**

1. **Typo in cache name** - Show cache not found error
2. **Wrong scope** - Show SW not intercepting
3. **Forgot to activate** - Show SW stuck in waiting

### Step 3.6: Hands-on Exercise (10 minutes)

**What to Say:**
> "Now it's your turn. I want you to add caching for the CSS and JavaScript files using CacheFirst strategy."

**Exercise:**
Participants should add to their `pwa.yaml`:

```yaml
- match: '/build/*'
  strategy: 'CacheFirst'
  cache_name: 'static-assets'
  max_entries: 60
  max_age: 7776000  # 90 days
```

**Then:**
- Clear cache
- Restart app
- Verify assets are cached
- Test they load offline

**Walk around and help!**

**Solution Review:**
- Show the working configuration
- Demonstrate it working offline
- Answer questions

**Checkpoint:**
> "Great! You now have a working Service Worker with caching strategies. After lunch, we'll make the whole app work offline with Background Sync!"

---

## Section 4: Basic Offline Mode (1 hour)

### Preparation
- [ ] Have offline.html.twig prepared
- [ ] Have network detection code ready
- [ ] Have offline indicator UI prepared

### Step 4.1: Offline Strategy Planning (10 minutes)

**What to Say:**
> "Let's think about what FieldLog needs offline. What should work? What can't work?"

**Facilitate Discussion:**
- What content can be cached? (viewed entries, images)
- What actions can work offline? (reading cached entries)
- What needs network? (creating new entries - for now, we'll fix this with sync later)
- What should we tell users? (offline indicator, what's available)

**Write on Whiteboard:**
```
Offline Capabilities:
✅ View previously loaded entries
✅ View cached images
✅ Navigate cached pages
❌ Create new entries (coming with Background Sync)
❌ Upload files
❌ Load new content
```

### Step 4.2: Create Offline Fallback Page (15 minutes)

**What to Say:**
> "When a network request fails and we don't have a cached version, we need a friendly fallback page."

**Guide Participants:**

1. **Create offline template `templates/pwa/offline.html.twig`:**

```twig
{% extends 'base.html.twig' %}

{% block title %}Offline - FieldLog{% endblock %}

{% block body %}
<div class="text-center py-12">
    <svg class="w-24 h-24 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 2.829a4.978 4.978 0 01-1.414-2.83m-1.414 5.658a9 9 0 01-2.167-9.238m7.824 2.167a1 1 0 111.414 1.414m-1.414-1.414L3 3m8.293 8.293l1.414 1.414" />
    </svg>

    <h1 class="text-2xl font-bold text-gray-900 mb-2">You're Offline</h1>
    <p class="text-gray-600 mb-6">
        This page isn't available offline yet.
    </p>

    <div class="space-y-4">
        <p class="text-sm text-gray-500">
            You can still:
        </p>
        <ul class="text-sm text-gray-700 space-y-2">
            <li>✓ View previously loaded entries</li>
            <li>✓ Browse cached pages</li>
        </ul>
    </div>

    <a href="/" class="inline-block mt-8 px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
        Go to Homepage
    </a>
</div>
{% endblock %}
```

2. **Create controller `src/Controller/PwaController.php`:**

```php
<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PwaController extends AbstractController
{
    #[Route('/offline', name: 'app_offline')]
    public function offline(): Response
    {
        return $this->render('pwa/offline.html.twig');
    }
}
```

3. **Add offline page to precache in `config/packages/pwa.yaml`:**

```yaml
precache:
    - '/assets/app.css'
    - '/assets/app.js'
    - '/'
    - '/offline'  # Add this
```

4. **Configure offline fallback in Service Worker config:**

```yaml
serviceworker:
    # ... existing config ...
    offline_fallback: '/offline'
```

**Test:**
```bash
castor console cache:clear
castor restart
```

- Visit homepage
- Go to DevTools → Application → Cache Storage
- Verify /offline is cached
- Go offline
- Navigate to uncached page
- Should see offline page

### Step 4.3: Implement Network Status Detection (15 minutes)

**What to Say:**
> "Users should know when they're offline. Let's add a visual indicator."

**Guide Participants:**

1. **Create Stimulus controller `assets/controllers/offline_controller.js`:**

```javascript
import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['indicator'];

    connect() {
        this.updateStatus();
        window.addEventListener('online', () => this.updateStatus());
        window.addEventListener('offline', () => this.updateStatus());
    }

    disconnect() {
        window.removeEventListener('online', () => this.updateStatus());
        window.removeEventListener('offline', () => this.updateStatus());
    }

    updateStatus() {
        const isOnline = navigator.onLine;

        if (this.hasIndicatorTarget) {
            this.indicatorTarget.classList.toggle('hidden', isOnline);
        }

        // Dispatch custom event for other components
        this.dispatch('statusChanged', {
            detail: { online: isOnline }
        });
    }
}
```

2. **Add indicator to `templates/base.html.twig`:**

```twig
<body data-controller="offline">
    <!-- Offline Indicator -->
    <div data-offline-target="indicator"
         class="hidden fixed top-0 left-0 right-0 bg-yellow-500 text-white text-center py-2 z-50">
        <span class="font-semibold">You're offline.</span>
        Some features may be unavailable.
    </div>

    <!-- Rest of content -->
    <div class="min-h-screen bg-base-100 text-base-content p-4 flex flex-col items-center">
        <!-- ... -->
    </div>
</body>
```

3. **Test:**
- Open app
- Go offline in DevTools
- See yellow banner appear
- Go online
- See banner disappear

### Step 4.4: Cache Entry Pages for Offline Viewing (15 minutes)

**What to Say:**
> "Let's make sure entry detail pages work offline once they've been viewed."

**Guide Participants:**

Add to `config/packages/pwa.yaml`:

```yaml
workbox:
    # ... existing strategies ...

    # Cache entry pages
    - match: '/entry/*'
      strategy: 'NetworkFirst'
      cache_name: 'entries'
      max_entries: 30
      max_age: 86400  # 1 day

    # Cache images
    - match: '/uploads/*'
      strategy: 'CacheFirst'
      cache_name: 'images'
      max_entries: 100
      max_age: 2592000  # 30 days
```

**Test Scenario:**
```bash
1. Visit homepage (online)
2. Click on several entries to view them
3. Go offline
4. Navigate back to those entries
5. They should load from cache!
```

**Walk Around:**
- Help participants test
- Debug caching issues
- Show DevTools cache inspection

### Step 4.5: Hands-on Exercise & Testing (15 minutes)

**What to Say:**
> "Now let's test the complete offline experience. I want you to follow this scenario."

**Exercise Steps:**

1. **Start online:**
   - Clear all caches
   - Unregister Service Worker
   - Reload app (fresh start)

2. **Browse content:**
   - View homepage
   - Click on 3-4 entries
   - View entry details
   - Load some images

3. **Go offline:**
   - DevTools → Network → Offline
   - Or: Airplane mode on device

4. **Test offline:**
   - Reload homepage (should work)
   - Navigate to entries you viewed (should work)
   - Try to view entry you didn't view yet (should show offline page)
   - Try to create new entry (should fail - we'll fix this next!)

5. **Go back online:**
   - Disable offline mode
   - See offline indicator disappear
   - Everything works normally

**Walk Around:**
- Help debug issues
- Show how to inspect caches
- Verify everyone has working offline mode

**Checkpoint:**
> "Excellent! FieldLog now works offline for content you've already viewed. After the break, we'll implement Background Sync so you can create entries offline too!"

---

## Section 5: Background Synchronization (1.5 hours)

### Preparation
- [ ] Have Background Sync concept diagram ready
- [ ] Have IndexedDB/localStorage code ready
- [ ] Test sync thoroughly before workshop
- [ ] Have DevTools Background Services tab ready

### Step 5.1: Background Sync Concept (10 minutes)

**What to Say:**
> "Background Sync is one of the coolest PWA features. It lets users take actions offline, and we automatically sync them when connection returns - even if they close the browser!"

**Draw on Whiteboard:**

```
User Story:
1. User is offline
2. Creates journal entry ──┐
3. Closes browser        │
4. Goes to sleep         │  Stored
5. Wakes up              │  locally
6. Phone reconnects      │
7. Background Sync! ◄────┘
8. Entry synced to server
9. User sees it synced ✓
```

**Key Points:**
- User doesn't wait for network
- App feels fast and responsive
- Sync happens automatically
- Resilient to network failures
- Retries on failure

**Browser Support:**
> "Background Sync is supported in Chrome, Edge, and Samsung Internet. For Safari and Firefox, we'll use a fallback approach."

**Show DevTools:**
- Application tab → Background Services → Background Sync
- Shows registered sync events
- Shows sync history

### Step 5.2: Architecture Planning (15 minutes)

**What to Say:**
> "Let's think through how this works. We need to store entries locally, sync them when online, and handle conflicts."

**Draw Architecture:**

```
┌─────────────────┐
│   Create Entry  │
│   Form Submit   │
└────────┬────────┘
         │
    Online? ─────Yes────► Send to Server ► Success!
         │
        No
         │
         ▼
┌─────────────────┐
│  Save to Local  │
│  IndexedDB      │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Register Sync   │
│  Event: 'sync-entries' │
└────────┬────────┘
         │
    [Wait for Network]
         │
         ▼
┌─────────────────┐
│  Sync Event     │
│  Fires          │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Get from Local  │
│ Send to Server  │
└────────┬────────┘
         │
    Success? ───Yes──► Remove from Local ► Show Notification
         │
        No
         │
         ▼
    Retry later
```

**Discuss:**
- Where to store pending entries? (IndexedDB for large data, localStorage for simple cases)
- How to identify pending vs synced? (Add a `status` field)
- What if sync fails? (Browser retries automatically)
- How to notify user? (Push notification or on next visit)

### Step 5.3: Implement Local Storage (20 minutes)

**What to Say:**
> "First, we need to store entries locally when offline. We'll use localStorage for simplicity, but IndexedDB is better for production."

**Guide Participants:**

1. **Create sync manager `assets/pwa/sync-manager.js`:**

```javascript
// Simple sync queue manager
export class SyncManager {
    constructor() {
        this.STORAGE_KEY = 'fieldlog_pending_entries';
    }

    // Add entry to sync queue
    async addToQueue(entryData) {
        const queue = this.getQueue();
        const entry = {
            id: `temp_${Date.now()}`,
            data: entryData,
            createdAt: new Date().toISOString(),
            status: 'pending'
        };
        queue.push(entry);
        localStorage.setItem(this.STORAGE_KEY, JSON.stringify(queue));

        // Register sync if supported
        if ('serviceWorker' in navigator && 'sync' in window.ServiceWorkerRegistration.prototype) {
            const registration = await navigator.serviceWorker.ready;
            await registration.sync.register('sync-entries');
        } else {
            // Fallback: try to sync immediately
            await this.syncAll();
        }

        return entry;
    }

    // Get pending entries
    getQueue() {
        const data = localStorage.getItem(this.STORAGE_KEY);
        return data ? JSON.parse(data) : [];
    }

    // Remove entry from queue
    removeFromQueue(entryId) {
        let queue = this.getQueue();
        queue = queue.filter(e => e.id !== entryId);
        localStorage.setItem(this.STORAGE_KEY, JSON.stringify(queue));
    }

    // Sync all pending entries
    async syncAll() {
        const queue = this.getQueue();
        const synced = [];
        const failed = [];

        for (const entry of queue) {
            try {
                const response = await fetch('/entry/new', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(entry.data)
                });

                if (response.ok) {
                    synced.push(entry.id);
                } else {
                    failed.push(entry.id);
                }
            } catch (error) {
                failed.push(entry.id);
            }
        }

        // Remove successfully synced entries
        synced.forEach(id => this.removeFromQueue(id));

        return { synced: synced.length, failed: failed.length };
    }

    // Get count of pending entries
    getPendingCount() {
        return this.getQueue().length;
    }
}
```

2. **Create Stimulus controller `assets/controllers/entry_form_controller.js`:**

```javascript
import { Controller } from '@hotwired/stimulus';
import { SyncManager } from '../pwa/sync-manager';

export default class extends Controller {
    static targets = ['form', 'status'];

    async connect() {
        this.syncManager = new SyncManager();
        this.updatePendingCount();

        // Listen for online/offline
        window.addEventListener('online', () => this.handleOnline());
    }

    async submit(event) {
        // Check if online
        if (!navigator.onLine) {
            event.preventDefault();
            await this.handleOfflineSubmit();
        }
        // If online, let form submit normally
    }

    async handleOfflineSubmit() {
        const formData = new FormData(this.formTarget);
        const entryData = {
            title: formData.get('title'),
            content: formData.get('content'),
            type: formData.get('type')
        };

        // Add to sync queue
        await this.syncManager.addToQueue(entryData);

        // Show success message
        this.showMessage('Entry saved offline. Will sync when online.', 'warning');

        // Update count
        this.updatePendingCount();

        // Clear form
        this.formTarget.reset();
    }

    async handleOnline() {
        const result = await this.syncManager.syncAll();
        if (result.synced > 0) {
            this.showMessage(`${result.synced} entry(s) synced successfully!`, 'success');
            this.updatePendingCount();
        }
    }

    updatePendingCount() {
        const count = this.syncManager.getPendingCount();
        if (this.hasStatusTarget && count > 0) {
            this.statusTarget.textContent = `${count} pending`;
            this.statusTarget.classList.remove('hidden');
        } else if (this.hasStatusTarget) {
            this.statusTarget.classList.add('hidden');
        }
    }

    showMessage(text, type) {
        // Dispatch event for flash message system
        this.dispatch('message', {
            detail: { text, type }
        });
    }
}
```

3. **Update entry form template `templates/entry/new.html.twig`:**

```twig
<div data-controller="entry-form offline">
    <!-- Pending count indicator -->
    <div data-entry-form-target="status"
         class="hidden mb-4 p-3 bg-yellow-100 text-yellow-800 rounded">
        Pending entries to sync
    </div>

    <form data-entry-form-target="form"
          data-action="submit->entry-form#submit"
          action="{{ path('app_entry_new') }}"
          method="post">
        <!-- Form fields -->
        {{ form_start(form) }}
        {{ form_widget(form) }}

        <button type="submit" class="btn btn-primary">
            <span data-offline-online>Save Entry</span>
            <span data-offline-offline class="hidden">Save Offline</span>
        </button>

        {{ form_end(form) }}
    </form>
</div>
```

**Test So Far:**
- Go offline
- Try to create entry
- See "saved offline" message
- Check localStorage in DevTools
- Go online
- Should auto-sync

### Step 5.4: Implement Service Worker Sync Event (20 minutes)

**What to Say:**
> "Now let's add the sync event handler to our Service Worker. This is what runs in the background."

**Guide Participants:**

1. **Update Service Worker config in `config/packages/pwa.yaml`:**

```yaml
serviceworker:
    # ... existing config ...

    # Enable background sync
    background_sync:
        enabled: true
```

2. **If bundle doesn't support custom sync handlers, create custom SW file:**

Create `public/custom-sw.js`:

```javascript
// Import the generated SW from bundle
importScripts('/sw.js');

// Add background sync handler
self.addEventListener('sync', (event) => {
    if (event.tag === 'sync-entries') {
        event.waitUntil(syncPendingEntries());
    }
});

async function syncPendingEntries() {
    try {
        // Get pending entries from IndexedDB or message to page
        const clients = await self.clients.matchAll();

        // Ask all clients to sync
        for (const client of clients) {
            client.postMessage({
                type: 'SYNC_ENTRIES'
            });
        }

        return Promise.resolve();
    } catch (error) {
        console.error('Sync failed:', error);
        // Sync will be retried by browser
        return Promise.reject(error);
    }
}
```

3. **Update app.js to handle sync messages:**

```javascript
// assets/app.js

if ('serviceWorker' in navigator) {
    // Listen for messages from SW
    navigator.serviceWorker.addEventListener('message', (event) => {
        if (event.data.type === 'SYNC_ENTRIES') {
            // Trigger sync from page
            const syncManager = new SyncManager();
            syncManager.syncAll().then(result => {
                if (result.synced > 0) {
                    // Show notification or update UI
                    console.log(`${result.synced} entries synced`);
                }
            });
        }
    });
}
```

**Walk Around:**
- Help with Service Worker configuration
- Debug sync registration
- Verify sync events fire

### Step 5.5: Test Background Sync (15 minutes)

**What to Say:**
> "Let's test the complete flow. This is the moment of truth!"

**Demo Test Scenario:**

**Scenario 1: Basic Offline Sync**
```bash
1. Go offline (DevTools)
2. Create a new entry
3. See "saved offline" message
4. Check localStorage - entry is there
5. Go online
6. Wait a few seconds
7. See sync notification
8. Entry appears in list!
```

**Scenario 2: Close and Reopen**
```bash
1. Go offline
2. Create entry
3. Close browser completely
4. Reconnect to network
5. Wait 30 seconds (SW sync happens)
6. Open browser
7. Entry is synced!
```

**Scenario 3: Multiple Entries**
```bash
1. Go offline
2. Create 3 entries
3. Check localStorage - 3 pending
4. Go online
5. All 3 sync
```

**DevTools Inspection:**
- Application → Background Services → Background Sync
- See registered sync events
- See sync history
- Check timestamps

**Guide Participants Through Tests:**
- Everyone try Scenario 1
- Walk around and help
- Celebrate when it works!

### Step 5.6: Handle Errors & Edge Cases (10 minutes)

**What to Say:**
> "Let's handle what happens when sync fails - maybe the server is down, or the user has no auth token."

**Discuss Edge Cases:**
1. Server returns 500 error → Browser retries automatically
2. Server returns 401 (auth) → Don't retry, show message to user
3. Network timeout → Retry
4. Duplicate entries → Use UUID to deduplicate

**Add Error Handling:**

Update `sync-manager.js`:

```javascript
async syncAll() {
    const queue = this.getQueue();
    const synced = [];
    const failed = [];

    for (const entry of queue) {
        try {
            const response = await fetch('/entry/new', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(entry.data)
            });

            if (response.ok) {
                synced.push(entry.id);
            } else if (response.status === 401) {
                // Auth error - don't retry
                this.markAsFailed(entry.id, 'Authentication required');
            } else {
                // Server error - will retry
                failed.push(entry.id);
            }
        } catch (error) {
            // Network error - will retry
            failed.push(entry.id);
        }
    }

    // Remove successfully synced
    synced.forEach(id => this.removeFromQueue(id));

    return { synced: synced.length, failed: failed.length };
}
```

**Checkpoint:**
> "Amazing work! You've implemented one of the most powerful PWA features. Users can now create entries completely offline, and they'll sync automatically. Let's take a break!"

---

## Section 6: Push Notifications (1 hour)

*[Continue with detailed instructions for remaining sections...]*

### Preparation
- [ ] Generate VAPID keys beforehand
- [ ] Test notification permissions
- [ ] Have example notifications ready
- [ ] Test on multiple devices

### Step 6.1: Push Notification Overview (10 minutes)

**What to Say:**
> "Push Notifications let us re-engage users even when they're not using the app. For FieldLog, we could send daily reminders to journal."

**Draw Architecture:**
```
Your Server ──► Push Service (Google/Apple) ──► User's Device ──► Notification
            (Web Push Protocol)
```

**Key Concepts:**
- VAPID keys (identification)
- Push subscription (user's endpoint)
- Notification payload
- Permission request

### Step 6.2: Generate VAPID Keys (10 minutes)

[Continue with detailed step-by-step...]

---

*[Due to length constraints, I'll note that the remaining sections (7-8) would follow the same detailed format with specific commands, code snippets, troubleshooting, and checkpoint reviews.]*

---

## Post-Workshop Actions

### Immediate (Day of Workshop)
- [ ] Send thank you email to participants
- [ ] Share links to completed code repository
- [ ] Send feedback survey
- [ ] Post photos/highlights on social media (with permission)

### Within 1 Week
- [ ] Review feedback survey results
- [ ] Update workshop materials based on feedback
- [ ] Send follow-up resources and links
- [ ] Offer office hours for questions

### Within 1 Month
- [ ] Check in with participants on their progress
- [ ] Consider follow-up advanced workshop
- [ ] Update this guide with lessons learned

---

## Notes Section

Use this space to track your own notes during delivery:

**What Worked Well:**
-
-
-

**What Needs Improvement:**
-
-
-

**Timing Adjustments:**
-
-
-

**Questions to Prepare Better For:**
-
-
-

**Technical Issues Encountered:**
-
-
-
