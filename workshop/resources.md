# PWA Workshop - Resources & References

This document contains all the resources, links, code snippets, and references you'll need to deliver the workshop successfully.

---

## Pre-Workshop Materials to Share with Participants

### Email Template: 2 Weeks Before

```
Subject: PWA Workshop Preparation - Action Required

Hi [Name],

Looking forward to seeing you at the PWA Workshop on [DATE]!

To ensure a smooth workshop experience, please complete these setup steps before the day:

**Required Software:**
- Docker Desktop (https://www.docker.com/products/docker-desktop)
- PHP 8.2+ (for Castor)
- Castor task runner: https://castor.jolicode.com/getting-started/installation/
- Modern web browser (Chrome or Edge recommended)
- Code editor of your choice

**Clone the Repository:**
```bash
git clone [REPOSITORY_URL]
cd FieldLog
```

**Build and Start:**
```bash
castor build
castor start
castor install
```

**Verify Setup:**
- Open https://localhost in your browser
- Accept the self-signed SSL certificate
- You should see the FieldLog homepage

**Optional but Recommended:**
- Bring a mobile device (Android preferred, iOS works too)
- Make sure you can connect to workshop WiFi
- Install Chrome on your mobile device

**WiFi Information:**
Network: [NETWORK_NAME]
Password: [Will be provided on the day]

If you encounter any issues, please reply to this email and we'll help you get set up.

See you soon!
[Your Name]
```

### Email Template: 1 Day Before

```
Subject: PWA Workshop Tomorrow - Final Reminders

Hi everyone!

Excited for tomorrow's PWA workshop!

**Quick Reminders:**
- Bring your laptop with FieldLog running
- Charge your devices (laptop + mobile if bringing)
- We start at [TIME] sharp

**What to Bring:**
- Laptop with setup completed
- Charger
- Mobile device (optional)
- Notebook for notes (optional)

**Schedule Overview:**
Morning: PWA fundamentals, manifest, Service Worker
Afternoon: Background Sync, Push Notifications, System Integration

**Workshop Etiquette:**
- Ask questions anytime!
- Help your neighbors if they get stuck
- Take breaks when needed
- Experiment and have fun

See you tomorrow!
[Your Name]
```

---

## Code Snippets Library

### Complete PWA Configuration

**`config/packages/pwa.yaml` - Complete Example:**

```yaml
pwa:
    manifest:
        name: 'FieldLog - Personal Journal'
        short_name: 'FieldLog'
        description: 'Your personal multimedia journaling application'
        theme_color: '#4f46e5'
        background_color: '#ffffff'
        display: 'standalone'
        start_url: '/'
        orientation: 'any'
        lang: 'en'
        dir: 'ltr'

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

        screenshots:
            - src: '/screenshots/home.png'
              sizes: '540x720'
              type: 'image/png'
              label: 'Home screen'

        shortcuts:
            - name: 'New Text Entry'
              short_name: 'Text'
              description: 'Create a new text entry'
              url: '/entry/new?type=text'
              icons:
                  - src: '/icons/shortcut-text.png'
                    sizes: '96x96'
            - name: 'New Link'
              short_name: 'Link'
              description: 'Save a new bookmark'
              url: '/entry/new?type=link'
              icons:
                  - src: '/icons/shortcut-link.png'
                    sizes: '96x96'
            - name: 'New File'
              short_name: 'File'
              description: 'Upload a new file'
              url: '/entry/new?type=file'
              icons:
                  - src: '/icons/shortcut-file.png'
                    sizes: '96x96'

        categories:
            - productivity
            - lifestyle

        share_target:
            action: '/entry/new'
            method: 'POST'
            enctype: 'multipart/form-data'
            params:
                title: 'title'
                text: 'content'
                url: 'url'
                files:
                    - name: 'file'
                      accept:
                          - 'image/*'
                          - 'application/pdf'
                          - 'text/plain'

    serviceworker:
        enabled: true
        src: '/sw.js'
        scope: '/'
        use_cache: true

        precache:
            - '/'
            - '/offline'
            - '/assets/app.css'
            - '/assets/app.js'
            - '/icons/icon-192.png'
            - '/icons/icon-512.png'

        workbox:
            enabled: true

            # Static assets - cache first
            - match: '/build/*'
              strategy: 'CacheFirst'
              cache_name: 'static-assets'
              max_entries: 60
              max_age: 7776000  # 90 days
              cacheable_response:
                  statuses: [0, 200]

            # Images - cache first
            - match: '/uploads/*'
              strategy: 'CacheFirst'
              cache_name: 'images'
              max_entries: 100
              max_age: 2592000  # 30 days
              cacheable_response:
                  statuses: [0, 200]

            # Entry pages - network first
            - match: '/entry/*'
              strategy: 'NetworkFirst'
              cache_name: 'entries'
              max_entries: 50
              max_age: 86400  # 1 day
              network_timeout: 3000  # 3 seconds

            # Homepage - stale while revalidate
            - match: '/'
              strategy: 'StaleWhileRevalidate'
              cache_name: 'homepage'
              max_age: 3600  # 1 hour

            # API calls - network first with short timeout
            - match: '/api/*'
              strategy: 'NetworkFirst'
              cache_name: 'api'
              max_entries: 20
              max_age: 300  # 5 minutes
              network_timeout: 2000

        offline_fallback: '/offline'

        background_sync:
            enabled: true
```

### Share Target Controller

**`src/Controller/ShareTargetController.php`:**

```php
<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShareTargetController extends AbstractController
{
    #[Route('/entry/new', name: 'app_share_target', methods: ['POST'])]
    public function receiveShare(Request $request): Response
    {
        // Get shared data
        $title = $request->request->get('title', '');
        $content = $request->request->get('content', '');
        $url = $request->request->get('url', '');
        $file = $request->files->get('file');

        // Determine entry type based on what was shared
        $type = 'text';
        if ($file !== null) {
            $type = 'file';
        } elseif (!empty($url)) {
            $type = 'link';
            if (empty($content)) {
                $content = $url;
            }
        }

        // Create entry
        $entry = new Entry();
        $entry->setTitle($title ?: 'Shared content');
        $entry->setContent($content);
        $entry->setType($type);
        $entry->setPublishedAt(new \DateTimeImmutable());

        if ($file !== null) {
            // Handle file upload
            // ... (your existing file upload logic)
        }

        $this->entityManager->persist($entry);
        $this->entityManager->flush();

        // Redirect to the created entry
        return $this->redirectToRoute('app_entry_show', [
            'id' => $entry->getId()
        ]);
    }
}
```

### Push Notification Setup

**`src/Service/PushNotificationService.php`:**

```php
<?php

namespace App\Service;

use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

class PushNotificationService
{
    private WebPush $webPush;

    public function __construct(
        string $vapidPublicKey,
        string $vapidPrivateKey,
        string $vapidSubject
    ) {
        $auth = [
            'VAPID' => [
                'subject' => $vapidSubject,
                'publicKey' => $vapidPublicKey,
                'privateKey' => $vapidPrivateKey,
            ],
        ];

        $this->webPush = new WebPush($auth);
    }

    public function sendNotification(
        string $endpoint,
        string $publicKey,
        string $authToken,
        array $payload
    ): bool {
        $subscription = Subscription::create([
            'endpoint' => $endpoint,
            'publicKey' => $publicKey,
            'authToken' => $authToken,
        ]);

        $notification = [
            'subscription' => $subscription,
            'payload' => json_encode($payload),
        ];

        $result = $this->webPush->sendOneNotification(
            $notification['subscription'],
            $notification['payload']
        );

        return $result->isSuccess();
    }

    public function sendDailyReminder(string $endpoint, string $p256dh, string $auth): bool
    {
        $payload = [
            'title' => 'Time to Journal! 📝',
            'body' => 'Take a moment to reflect on your day.',
            'icon' => '/icons/icon-192.png',
            'badge' => '/icons/badge.png',
            'tag' => 'daily-reminder',
            'requireInteraction' => false,
            'actions' => [
                [
                    'action' => 'write',
                    'title' => 'Write Entry'
                ],
                [
                    'action' => 'dismiss',
                    'title' => 'Later'
                ]
            ],
            'data' => [
                'url' => '/entry/new'
            ]
        ];

        return $this->sendNotification($endpoint, $p256dh, $auth, $payload);
    }
}
```

**Generate VAPID Keys Command:**

```bash
# Install web-push library
composer require minishlink/web-push

# Generate keys (use PHP)
php -r "
require 'vendor/autoload.php';
\$keys = \Minishlink\WebPush\VAPID::createVapidKeys();
echo 'Public Key: ' . \$keys['publicKey'] . PHP_EOL;
echo 'Private Key: ' . \$keys['privateKey'] . PHP_EOL;
"

# Save to .env.local
# VAPID_PUBLIC_KEY=...
# VAPID_PRIVATE_KEY=...
# VAPID_SUBJECT=mailto:your@email.com
```

### Frontend Assets

**`assets/pwa/install-prompt.js`:**

```javascript
export class InstallPrompt {
    constructor() {
        this.deferredPrompt = null;
        this.setupListeners();
    }

    setupListeners() {
        // Capture the install prompt event
        window.addEventListener('beforeinstallprompt', (e) => {
            // Prevent default mini-infobar
            e.preventDefault();
            // Store for later use
            this.deferredPrompt = e;
            // Show custom install button
            this.showInstallButton();
        });

        // Detect if app was installed
        window.addEventListener('appinstalled', () => {
            this.deferredPrompt = null;
            this.hideInstallButton();
            console.log('PWA installed successfully');
        });
    }

    showInstallButton() {
        const button = document.getElementById('install-button');
        if (button) {
            button.classList.remove('hidden');
            button.addEventListener('click', () => this.promptInstall());
        }
    }

    hideInstallButton() {
        const button = document.getElementById('install-button');
        if (button) {
            button.classList.add('hidden');
        }
    }

    async promptInstall() {
        if (!this.deferredPrompt) {
            return;
        }

        // Show install prompt
        this.deferredPrompt.prompt();

        // Wait for user response
        const { outcome } = await this.deferredPrompt.userChoice;

        console.log(`Install prompt outcome: ${outcome}`);

        // Clear the saved prompt
        this.deferredPrompt = null;
        this.hideInstallButton();
    }

    // Check if app is already installed
    static isInstalled() {
        // Check if running in standalone mode
        return window.matchMedia('(display-mode: standalone)').matches ||
               window.navigator.standalone === true;
    }
}

// Auto-initialize
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        new InstallPrompt();
    });
} else {
    new InstallPrompt();
}
```

**`assets/pwa/update-checker.js`:**

```javascript
export class UpdateChecker {
    constructor() {
        this.registration = null;
        this.init();
    }

    async init() {
        if (!('serviceWorker' in navigator)) {
            return;
        }

        try {
            this.registration = await navigator.serviceWorker.ready;
            this.checkForUpdates();

            // Check for updates periodically
            setInterval(() => this.checkForUpdates(), 60000); // Every minute
        } catch (error) {
            console.error('Service Worker initialization error:', error);
        }
    }

    async checkForUpdates() {
        if (!this.registration) {
            return;
        }

        try {
            await this.registration.update();
        } catch (error) {
            console.error('Update check failed:', error);
        }
    }

    // Listen for new service worker
    listenForUpdates() {
        if (!this.registration) {
            return;
        }

        this.registration.addEventListener('updatefound', () => {
            const newWorker = this.registration.installing;

            newWorker.addEventListener('statechange', () => {
                if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                    // New service worker available
                    this.showUpdateNotification();
                }
            });
        });
    }

    showUpdateNotification() {
        // Show user-friendly update prompt
        const updateBanner = document.createElement('div');
        updateBanner.className = 'fixed top-0 left-0 right-0 bg-blue-600 text-white p-4 z-50 text-center';
        updateBanner.innerHTML = `
            <p class="mb-2">A new version of FieldLog is available!</p>
            <button onclick="window.location.reload()" class="bg-white text-blue-600 px-4 py-2 rounded">
                Update Now
            </button>
        `;
        document.body.prepend(updateBanner);
    }
}

// Auto-initialize
new UpdateChecker();
```

**`assets/pwa/notification-manager.js`:**

```javascript
export class NotificationManager {
    constructor() {
        this.vapidPublicKey = document.querySelector('meta[name="vapid-public-key"]')?.content;
    }

    // Request notification permission
    async requestPermission() {
        if (!('Notification' in window)) {
            console.log('This browser does not support notifications');
            return false;
        }

        if (Notification.permission === 'granted') {
            return true;
        }

        if (Notification.permission !== 'denied') {
            const permission = await Notification.requestPermission();
            return permission === 'granted';
        }

        return false;
    }

    // Subscribe to push notifications
    async subscribe() {
        const hasPermission = await this.requestPermission();
        if (!hasPermission) {
            return null;
        }

        try {
            const registration = await navigator.serviceWorker.ready;

            const subscription = await registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: this.urlBase64ToUint8Array(this.vapidPublicKey)
            });

            // Send subscription to server
            await this.sendSubscriptionToServer(subscription);

            return subscription;
        } catch (error) {
            console.error('Failed to subscribe:', error);
            return null;
        }
    }

    // Unsubscribe from push notifications
    async unsubscribe() {
        try {
            const registration = await navigator.serviceWorker.ready;
            const subscription = await registration.pushManager.getSubscription();

            if (subscription) {
                await subscription.unsubscribe();
                await this.removeSubscriptionFromServer(subscription);
            }

            return true;
        } catch (error) {
            console.error('Failed to unsubscribe:', error);
            return false;
        }
    }

    // Check subscription status
    async getSubscription() {
        try {
            const registration = await navigator.serviceWorker.ready;
            return await registration.pushManager.getSubscription();
        } catch (error) {
            console.error('Failed to get subscription:', error);
            return null;
        }
    }

    // Send subscription to your server
    async sendSubscriptionToServer(subscription) {
        const response = await fetch('/api/push/subscribe', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(subscription)
        });

        if (!response.ok) {
            throw new Error('Failed to save subscription');
        }

        return response.json();
    }

    // Remove subscription from server
    async removeSubscriptionFromServer(subscription) {
        const response = await fetch('/api/push/unsubscribe', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(subscription)
        });

        return response.ok;
    }

    // Utility: Convert VAPID key
    urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding)
            .replace(/\-/g, '+')
            .replace(/_/g, '/');

        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);

        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }

        return outputArray;
    }

    // Show local notification
    async showNotification(title, options) {
        if (Notification.permission !== 'granted') {
            return;
        }

        try {
            const registration = await navigator.serviceWorker.ready;
            await registration.showNotification(title, options);
        } catch (error) {
            console.error('Failed to show notification:', error);
        }
    }
}
```

---

## Testing Checklists

### Installation Testing Checklist

**Desktop Chrome/Edge:**
- [ ] Manifest loads without errors
- [ ] Install button appears in omnibox
- [ ] Chrome menu shows "Install FieldLog" option
- [ ] Install prompt can be triggered
- [ ] App installs to desktop
- [ ] Installed app opens in standalone window
- [ ] App icon appears in Start Menu/Applications
- [ ] App appears in Windows/Mac app switcher
- [ ] Uninstall works correctly

**Android Chrome:**
- [ ] Mini-infobar appears (or install button)
- [ ] "Add to Home Screen" in menu
- [ ] Install prompt appears
- [ ] App installs to home screen
- [ ] Icon has correct shape and appearance
- [ ] Splash screen shows with correct colors
- [ ] App opens in standalone mode
- [ ] Status bar color matches theme_color
- [ ] App appears in app drawer
- [ ] App appears in recent apps
- [ ] Share sheet includes FieldLog
- [ ] Uninstall from app drawer works

**iOS Safari:**
- [ ] "Add to Home Screen" option available
- [ ] Icon appears on home screen
- [ ] Icon looks correct (not maskable)
- [ ] App opens in standalone mode
- [ ] Status bar is visible
- [ ] Limited PWA features work
- [ ] Delete from home screen works

### Service Worker Testing Checklist

**Registration:**
- [ ] SW registers successfully
- [ ] SW activates
- [ ] SW shows in DevTools → Application → Service Workers
- [ ] SW scope is correct
- [ ] No console errors

**Caching:**
- [ ] Precache assets appear in Cache Storage
- [ ] Static assets use CacheFirst
- [ ] Dynamic content uses NetworkFirst
- [ ] Images cache correctly
- [ ] API responses cache correctly
- [ ] Cache names are correct
- [ ] Max entries limit works
- [ ] Max age expiration works

**Updates:**
- [ ] New SW detects changes
- [ ] New SW installs alongside old
- [ ] New SW waits for tab close
- [ ] New SW activates on reopen
- [ ] Old caches are cleaned up
- [ ] "Update on reload" works in dev

**Offline:**
- [ ] Offline page caches
- [ ] Precached assets work offline
- [ ] Previously viewed pages work offline
- [ ] Uncached pages show offline fallback
- [ ] Network failures show offline fallback
- [ ] Images display offline
- [ ] CSS/JS load offline

### Background Sync Testing Checklist

**Registration:**
- [ ] Sync registers successfully
- [ ] Sync shows in DevTools → Background Services
- [ ] Sync tag is correct
- [ ] Multiple syncs can register

**Functionality:**
- [ ] Creating entry offline saves to queue
- [ ] Sync fires when back online
- [ ] Entries sync to server
- [ ] Synced entries removed from queue
- [ ] Multiple entries sync correctly
- [ ] Sync works after browser close
- [ ] Failed syncs retry
- [ ] Success callback fires

**Edge Cases:**
- [ ] Duplicate entries handled
- [ ] Server errors handled gracefully
- [ ] Auth errors prevent retry
- [ ] Network timeout retries
- [ ] Queue persists through restarts
- [ ] Large queue handles correctly

### Push Notification Testing Checklist

**Setup:**
- [ ] VAPID keys generated
- [ ] Public key accessible in page
- [ ] Subscription endpoint configured
- [ ] Server can send notifications

**Permission:**
- [ ] Permission request appears
- [ ] Permission stored correctly
- [ ] Denied permission handled
- [ ] Granted permission registers subscription
- [ ] Subscription saved to server

**Notifications:**
- [ ] Notification appears
- [ ] Icon displays correctly
- [ ] Badge displays correctly
- [ ] Title and body correct
- [ ] Actions appear
- [ ] Clicking notification opens app
- [ ] Actions trigger correct handlers
- [ ] Notification closes correctly
- [ ] Multiple notifications handled

**Platforms:**
- [ ] Works on Windows
- [ ] Works on Mac
- [ ] Works on Android
- [ ] Works on Linux
- [ ] iOS limitations documented

---

## External Resources

### Official Documentation

**PWA Fundamentals:**
- [Google Web.dev PWA](https://web.dev/progressive-web-apps/)
- [MDN PWA Guide](https://developer.mozilla.org/en-US/docs/Web/Progressive_web_apps)
- [Microsoft PWA Documentation](https://docs.microsoft.com/en-us/microsoft-edge/progressive-web-apps-chromium/)

**Specifications:**
- [Web App Manifest Spec](https://www.w3.org/TR/appmanifest/)
- [Service Worker Spec](https://w3c.github.io/ServiceWorker/)
- [Push API Spec](https://w3c.github.io/push-api/)
- [Background Sync Spec](https://wicg.github.io/background-sync/spec/)

**Symfony PWA Bundle:**
- [Spomky-Labs/PWA-Bundle GitHub](https://github.com/Spomky-Labs/pwa-bundle)
- [Bundle Documentation](https://spomky-labs.github.io/pwa-bundle/)
- [Symfony UX Documentation](https://ux.symfony.com/)

### Tools & Testing

**Development Tools:**
- [Lighthouse](https://developers.google.com/web/tools/lighthouse) - Audit tool
- [PWA Builder](https://www.pwabuilder.com/) - Validate and package PWAs
- [Maskable.app](https://maskable.app/) - Test maskable icons
- [Web.dev Measure](https://web.dev/measure/) - Online audit

**Testing Services:**
- [BrowserStack](https://www.browserstack.com/) - Cross-browser testing
- [LambdaTest](https://www.lambdatest.com/) - Real device testing
- [WebPageTest](https://www.webpagetest.org/) - Performance testing

**Push Notification Testing:**
- [Web Push Testing Tool](https://tests.peter.sh/notification-generator/)
- [Push Companion](https://web-push-book.gauntface.com/demos/notification-examples/)

### Learning Resources

**Articles & Tutorials:**
- [Your First PWA](https://codelabs.developers.google.com/codelabs/your-first-pwapp/)
- [Offline Cookbook](https://jakearchibald.com/2014/offline-cookbook/)
- [Service Worker Lifecycle](https://developers.google.com/web/fundamentals/primers/service-workers/lifecycle)
- [Workbox Guides](https://developers.google.com/web/tools/workbox/guides/get-started)

**Videos:**
- [Chrome Dev Summit PWA Talks](https://www.youtube.com/playlist?list=PLNYkxOF6rcIAKIQFsNbV0JDws_G_bnNo9)
- [Google I/O PWA Sessions](https://www.youtube.com/results?search_query=google+io+pwa)

**Books:**
- "Progressive Web Apps" by Dean Alan Hume
- "Building Progressive Web Apps" by Tal Ater

**Newsletters:**
- [PWA Weekly](https://pwaweekly.com/)
- [Web Platform News](https://webplatform.news/)

### Community

**Forums & Discussion:**
- [Stack Overflow #progressive-web-apps](https://stackoverflow.com/questions/tagged/progressive-web-apps)
- [Reddit r/PWA](https://www.reddit.com/r/PWA/)
- [PWA Slack Community](https://bit.ly/join-pwa-slack)

**Twitter Experts to Follow:**
- [@ChromiumDev](https://twitter.com/ChromiumDev)
- [@jaffathecake](https://twitter.com/jaffathecake) - Jake Archibald
- [@addyosmani](https://twitter.com/addyosmani) - Addy Osmani
- [@Paul_Kinlan](https://twitter.com/Paul_Kinlan) - Paul Kinlan

---

## Presentation Slides Outline

If you're creating slides, here's a suggested outline:

### Slide 1: Title
- PWA Workshop
- Building Progressive Web Apps with Symfony
- Your Name & Date

### Slide 2: What is a PWA?
- Definition
- Key characteristics
- Benefits

### Slide 3: PWA Requirements
- HTTPS
- Service Worker
- Web App Manifest

### Slide 4: Service Worker Lifecycle
- Diagram
- Install → Activate → Fetch

### Slide 5: Caching Strategies
- Cache First
- Network First
- Stale While Revalidate

### Slide 6: Manifest Properties
- Key properties list
- Example JSON

### Slide 7: Background Sync
- Use cases
- How it works

### Slide 8: Push Notifications
- Architecture
- VAPID
- Permissions

### Slide 9: System Integration
- Share Target
- Web Share
- Shortcuts
- Badging

### Slide 10: Browser Support
- caniuse.com screenshots
- Progressive enhancement

### Slide 11: Resources
- Links to docs
- Tools
- Community

### Slide 12: Next Steps
- Deploy to production
- App stores
- Advanced topics

---

## Handouts

### Quick Reference Card (Print & Distribute)

```
╔══════════════════════════════════════════════════════╗
║     PWA WORKSHOP - QUICK REFERENCE CARD               ║
╠══════════════════════════════════════════════════════╣
║ ESSENTIAL COMMANDS                                    ║
║                                                       ║
║ Start app:      castor start                         ║
║ Stop app:       castor stop                          ║
║ Clear cache:    castor console cache:clear           ║
║ Install deps:   castor install                       ║
║                                                       ║
╠══════════════════════════════════════════════════════╣
║ DEVTOOLS SHORTCUTS                                    ║
║                                                       ║
║ Open DevTools:  F12                                  ║
║ Service Worker: Application → Service Workers        ║
║ Cache Storage:  Application → Cache Storage          ║
║ Manifest:       Application → Manifest               ║
║ Go Offline:     Network → Throttling → Offline       ║
║                                                       ║
╠══════════════════════════════════════════════════════╣
║ CACHING STRATEGIES                                    ║
║                                                       ║
║ CacheFirst:     Cache → Network (static assets)      ║
║ NetworkFirst:   Network → Cache (dynamic content)    ║
║ StaleRevalidate: Cache + Network (frequently updated)║
║                                                       ║
╠══════════════════════════════════════════════════════╣
║ MANIFEST PROPERTIES                                   ║
║                                                       ║
║ name:           Full app name                        ║
║ short_name:     Home screen name (12 chars)          ║
║ start_url:      Launch URL                           ║
║ display:        standalone, fullscreen, minimal-ui   ║
║ theme_color:    Browser theme                        ║
║ background_color: Splash screen                      ║
║ icons:          192x192, 512x512, maskable           ║
║                                                       ║
╠══════════════════════════════════════════════════════╣
║ TROUBLESHOOTING                                       ║
║                                                       ║
║ SW not updating:                                      ║
║   ☑ Check "Update on reload" in DevTools             ║
║   ☑ Unregister SW and reload                         ║
║   ☑ Hard refresh (Ctrl+Shift+R)                      ║
║                                                       ║
║ Cache issues:                                         ║
║   ☑ Clear Cache Storage in DevTools                  ║
║   ☑ Check SW fetch events                            ║
║   ☑ Verify cache names                               ║
║                                                       ║
║ Install not working:                                  ║
║   ☑ Verify HTTPS                                     ║
║   ☑ Check manifest has icons                         ║
║   ☑ Ensure SW is active                              ║
║   ☑ Run Lighthouse audit                             ║
║                                                       ║
╠══════════════════════════════════════════════════════╣
║ USEFUL LINKS                                          ║
║                                                       ║
║ Workshop repo:  [YOUR_REPO_URL]                      ║
║ PWA Bundle:     github.com/Spomky-Labs/pwa-bundle   ║
║ Web.dev:        web.dev/progressive-web-apps         ║
║ MDN:            mdn.io/pwa                           ║
║ Lighthouse:     developers.google.com/web/lighthouse║
║                                                       ║
╚══════════════════════════════════════════════════════╝
```

---

## Assets to Prepare

### Icons

**Required Sizes:**
- 192x192 px (minimum for PWA)
- 512x512 px (minimum for PWA)
- 512x512 px maskable (Android adaptive icon)
- 96x96 px (shortcuts)
- 72x72 px (badge)

**Icon Generation Service:**
Use https://realfavicongenerator.net/ or provide these ImageMagick commands:

```bash
# From a single source image (1024x1024 recommended)
convert icon-source.png -resize 192x192 icon-192.png
convert icon-source.png -resize 512x512 icon-512.png

# For maskable (add safe zone padding)
convert icon-source.png -resize 420x420 -gravity center -extent 512x512 icon-maskable.png
```

### Screenshots

For manifest `screenshots` array:
- 540x720 px (mobile portrait)
- 720x540 px (mobile landscape)
- 1280x720 px (desktop)

---

## Post-Workshop Follow-up Materials

### Email Template: Day After Workshop

```
Subject: PWA Workshop - Thank You & Resources

Hi everyone,

Thank you for participating in yesterday's PWA workshop! It was great to see all your apps coming to life.

**Workshop Materials:**
- Code repository: [URL]
- Presentation slides: [URL]
- Additional resources: [URL]

**Next Steps:**
1. Complete any unfinished sections
2. Deploy your PWA to production (remember: HTTPS required!)
3. Submit your app to PWA directories
4. Consider publishing to app stores (optional)

**Got Questions?**
- Email me anytime: [YOUR_EMAIL]
- Join our Slack channel: [SLACK_URL]
- Office hours: [TIMES]

**Feedback Requested:**
Please take 3 minutes to fill out this survey: [SURVEY_URL]
Your feedback helps improve future workshops!

**Want More?**
Let me know if you'd be interested in:
- Advanced PWA workshop (performance, advanced APIs)
- One-on-one consultation
- Company/team training

Keep building awesome PWAs!

[Your Name]
```

---

This resources document should give you everything you need to deliver a successful workshop!
