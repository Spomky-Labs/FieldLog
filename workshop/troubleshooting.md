# PWA Workshop - Troubleshooting Guide

This guide covers common issues you'll encounter during the workshop and how to resolve them quickly.

---

## Quick Diagnostic Checklist

When something isn't working, run through this checklist:

```
[ ] Is the app running? (Check https://localhost)
[ ] Is HTTPS working? (Check for SSL errors)
[ ] Is the Service Worker registered? (DevTools → Application → Service Workers)
[ ] Is the manifest loading? (DevTools → Application → Manifest)
[ ] Are there console errors? (DevTools → Console)
[ ] Has cache been cleared? (Try hard refresh: Ctrl+Shift+R)
[ ] Is the bundle configured correctly? (Check config/packages/pwa.yaml)
[ ] Has Symfony cache been cleared? (castor console cache:clear)
```

---

## Setup & Installation Issues

### Issue: Composer fails to install the bundle

**Symptoms:**
```
Could not find package spomky-labs/pwa-bundle
```

**Solutions:**

1. **Check Composer version:**
```bash
composer --version
# Should be 2.x
```

2. **Update Composer:**
```bash
composer self-update
```

3. **Clear Composer cache:**
```bash
composer clear-cache
composer require spomky-labs/pwa-bundle
```

4. **Try with dependencies:**
```bash
composer require spomky-labs/pwa-bundle --with-all-dependencies
```

5. **Check minimum stability:**
```bash
# In composer.json, ensure:
"minimum-stability": "stable"
```

---

### Issue: Docker containers won't start

**Symptoms:**
```
Error: port 443 already in use
Error: Cannot connect to Docker daemon
```

**Solutions:**

1. **Check if port is in use:**
```bash
# Linux/Mac:
lsof -i :443
netstat -an | grep 443

# Windows:
netstat -ano | findstr :443
```

2. **Stop conflicting services:**
```bash
# Apache
sudo service apache2 stop

# Nginx
sudo service nginx stop
```

3. **Restart Docker:**
```bash
# Mac/Windows: Restart Docker Desktop
# Linux:
sudo systemctl restart docker
```

4. **Use different ports:**
Edit `compose.yaml`:
```yaml
ports:
    - "8443:443"  # Use 8443 instead
```

5. **Check Docker daemon:**
```bash
docker ps
# If this fails, Docker daemon isn't running
```

---

### Issue: SSL certificate errors

**Symptoms:**
```
NET::ERR_CERT_AUTHORITY_INVALID
Your connection is not private
```

**Solutions:**

1. **Accept the certificate:**
   - Click "Advanced"
   - Click "Proceed to localhost (unsafe)"
   - This is normal for local development

2. **Install certificate permanently (Chrome):**
   - Visit `chrome://settings/certificates`
   - Go to "Authorities"
   - Import the certificate from `.docker/certs/`

3. **Firefox specific:**
   - Click the lock icon
   - "Connection not secure" → "More Information"
   - "View Certificate" → "Add Exception"

4. **If using custom domain:**
```bash
# Regenerate certificates
castor destroy
castor build
castor start
```

---

### Issue: Castor command not found

**Symptoms:**
```bash
castor: command not found
```

**Solutions:**

1. **Install Castor:**
```bash
# Using Composer (globally)
composer global require jolicode/castor

# Or download manually
curl -LO https://github.com/jolicode/castor/releases/latest/download/castor.linux-amd64
chmod +x castor.linux-amd64
sudo mv castor.linux-amd64 /usr/local/bin/castor
```

2. **Check PATH:**
```bash
# Ensure Composer bin is in PATH
export PATH="$HOME/.composer/vendor/bin:$PATH"

# Add to ~/.bashrc or ~/.zshrc to persist
```

3. **Use PHP directly:**
```bash
php castor.php [command]
```

---

## Manifest Issues

### Issue: Manifest not loading

**Symptoms:**
- 404 error on `/manifest.json`
- DevTools shows "No manifest detected"

**Solutions:**

1. **Check route exists:**
```bash
castor console debug:router | grep manifest
# Should show a manifest route
```

2. **Clear Symfony cache:**
```bash
castor console cache:clear
castor restart
```

3. **Verify bundle is registered:**
```bash
cat config/bundles.php | grep PWA
# Should show: SpomkyLabs\PwaBundle\SpomkyLabsPwaBundle::class
```

4. **Check configuration file exists:**
```bash
ls -la config/packages/pwa.yaml
```

5. **Test manifest directly:**
```bash
curl -k https://localhost/manifest.json
# Should return JSON
```

6. **Check for YAML syntax errors:**
```bash
castor console lint:yaml config/packages/pwa.yaml
```

---

### Issue: Manifest validation errors

**Symptoms:**
- DevTools shows manifest errors
- "start_url" not within scope
- "Invalid icon format"

**Solutions:**

1. **Common errors and fixes:**

**Error:** `start_url must be same origin as document`
```yaml
# Fix: Use relative URL
manifest:
    start_url: '/'  # Not https://localhost/
```

**Error:** `Icon must be at least 192x192`
```yaml
# Fix: Ensure icon sizes
icons:
    - src: '/icons/icon-192.png'
      sizes: '192x192'  # Must be at least 192x192
```

**Error:** `Invalid MIME type`
```yaml
# Fix: Add type
icons:
    - src: '/icons/icon-192.png'
      sizes: '192x192'
      type: 'image/png'  # Add this
```

2. **Validate manifest:**
   - Use DevTools → Application → Manifest
   - Check for red errors
   - Use https://www.pwabuilder.com/ to validate

3. **Test icon paths:**
```bash
curl -I https://localhost/icons/icon-192.png
# Should return 200 OK
```

---

### Issue: App not installable

**Symptoms:**
- No install button appears
- Lighthouse says "Not installable"

**Solutions:**

1. **Check PWA criteria:**
   - [ ] Valid manifest with icons
   - [ ] Service Worker registered and active
   - [ ] Service Worker has fetch event handler
   - [ ] HTTPS (or localhost)
   - [ ] Site engagement (automatic prompt only)

2. **Run Lighthouse audit:**
```bash
# In Chrome DevTools:
# Lighthouse → Progressive Web App → Generate report
```

3. **Check for manifest errors:**
```javascript
// In console:
console.log(await navigator.serviceWorker.ready);
// Should show registered SW
```

4. **Force install prompt:**
   - Manual install: Chrome menu → "Install FieldLog"
   - Or: Click install icon in address bar (if available)

5. **Check engagement heuristics:**
   - User must interact with page
   - Must be on page for ~30 seconds
   - Or: Use `beforeinstallprompt` event to control manually

---

## Service Worker Issues

### Issue: Service Worker not registering

**Symptoms:**
- "Service Worker registration failed"
- No SW in DevTools Application tab

**Solutions:**

1. **Check HTTPS:**
   - Service Workers require HTTPS (except localhost)
   - Verify you're on https://localhost, not http://

2. **Check for JavaScript errors:**
```javascript
// In console:
if ('serviceWorker' in navigator) {
    console.log('SW supported');
} else {
    console.log('SW NOT supported');
}
```

3. **Check SW file exists:**
```bash
curl -k https://localhost/sw.js
# Should return JavaScript code
```

4. **Check registration code:**
```javascript
// Should be in your app.js or similar:
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/sw.js')
        .then(reg => console.log('SW registered', reg))
        .catch(err => console.error('SW registration failed', err));
}
```

5. **Check browser support:**
   - Chrome/Edge: ✅
   - Firefox: ✅
   - Safari: ✅
   - Opera: ✅
   - IE: ❌

6. **Clear registration and retry:**
```javascript
// In console:
navigator.serviceWorker.getRegistrations().then(registrations => {
    registrations.forEach(reg => reg.unregister());
    location.reload();
});
```

---

### Issue: Service Worker stuck in "waiting" state

**Symptoms:**
- New SW installs but doesn't activate
- DevTools shows "waiting to activate"

**Solutions:**

1. **Skip waiting (Development only):**
   - DevTools → Application → Service Workers
   - Click "skipWaiting" button

2. **Close all tabs:**
   - Close ALL tabs of the site
   - Reopen
   - SW should activate

3. **Enable "Update on reload":**
   - DevTools → Application → Service Workers
   - Check "Update on reload"
   - Refresh page

4. **Force activation in code:**
```javascript
// In sw.js:
self.addEventListener('install', (event) => {
    self.skipWaiting();  // Force skip waiting
});

self.addEventListener('activate', (event) => {
    event.waitUntil(clients.claim());  // Take control immediately
});
```

5. **Hard refresh:**
   - Ctrl+Shift+R (Chrome/Firefox)
   - Cmd+Shift+R (Mac)

---

### Issue: Service Worker not updating

**Symptoms:**
- Code changes don't appear
- Old SW keeps running

**Solutions:**

1. **Check byte-for-byte change:**
   - SW only updates if file changed
   - Even 1 byte difference triggers update
   - Comments count as changes

2. **Force update:**
```javascript
// In console:
navigator.serviceWorker.getRegistration().then(reg => {
    reg.update();
});
```

3. **Clear cache and reload:**
   - DevTools → Application → Clear storage
   - Check all boxes
   - "Clear site data"
   - Reload

4. **Unregister and reload:**
```javascript
// In console:
navigator.serviceWorker.getRegistrations().then(registrations => {
    registrations.forEach(reg => reg.unregister());
});
// Then reload page
```

5. **Check HTTP caching:**
```bash
# SW file should not be cached by HTTP
# Check headers:
curl -I https://localhost/sw.js
# Cache-Control should be: max-age=0 or no-cache
```

6. **Disable cache in DevTools:**
   - DevTools → Network tab
   - Check "Disable cache"
   - Keep DevTools open

---

### Issue: Service Worker not intercepting requests

**Symptoms:**
- Requests go to network, not SW
- Network tab doesn't show "from ServiceWorker"

**Solutions:**

1. **Check SW is activated:**
   - DevTools → Application → Service Workers
   - Status should be "activated and running"

2. **Check fetch event listener exists:**
```javascript
// In sw.js, should have:
self.addEventListener('fetch', (event) => {
    // Handle request
});
```

3. **Check SW scope:**
```javascript
// In console:
navigator.serviceWorker.getRegistration().then(reg => {
    console.log('Scope:', reg.scope);
});
// Should be: https://localhost/
```

4. **Check request is in scope:**
   - SW only intercepts requests within its scope
   - If SW is at `/app/sw.js`, scope is `/app/`
   - Won't intercept requests to `/api/`

5. **Check for CORS issues:**
   - SW can only intercept same-origin requests
   - Or requests with CORS headers

6. **Reload page:**
   - SW doesn't control page on first load
   - Reload to let SW take control

---

### Issue: Caching not working

**Symptoms:**
- Assets not cached
- Cache Storage is empty
- Requests always go to network

**Solutions:**

1. **Check Cache Storage in DevTools:**
   - Application → Cache Storage
   - Should see cache names
   - Should see cached URLs

2. **Check precache configuration:**
```yaml
# config/packages/pwa.yaml
serviceworker:
    precache:
        - '/'
        - '/assets/app.css'
        - '/assets/app.js'
```

3. **Check install event:**
```javascript
// sw.js should cache during install:
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open('my-cache').then(cache => {
            return cache.addAll([/* URLs */]);
        })
    );
});
```

4. **Check fetch event returns cached responses:**
```javascript
self.addEventListener('fetch', (event) => {
    event.respondWith(
        caches.match(event.request).then(response => {
            return response || fetch(event.request);
        })
    );
});
```

5. **Check for cache errors:**
```javascript
// In SW console (DevTools → Console → Filter by "Service Worker")
// Look for cache errors
```

6. **Clear and retry:**
   - Delete all caches in DevTools
   - Unregister SW
   - Reload and check if caches populate

---

## Offline Mode Issues

### Issue: Offline mode not working

**Symptoms:**
- App doesn't work offline
- "No internet connection" error
- Blank page offline

**Solutions:**

1. **Check offline testing method:**
   - Use DevTools Network throttling (recommended)
   - Don't use airplane mode while DevTools is open
   - "Offline" in Network tab, not "Slow 3G"

2. **Check assets are cached:**
   - Go online
   - Load page fully
   - Check Cache Storage has assets
   - Then go offline

3. **Check Network strategies:**
```yaml
# For offline support, use:
workbox:
    - match: '/assets/*'
      strategy: 'CacheFirst'  # Not 'NetworkFirst'
```

4. **Check offline fallback:**
```yaml
serviceworker:
    offline_fallback: '/offline'
```

5. **Verify offline page is cached:**
```javascript
// Should be in precache list:
precache:
    - '/offline'
```

6. **Test step-by-step:**
   - Online: Load homepage
   - Online: Check Cache Storage has assets
   - Offline: Reload homepage
   - Should work

---

### Issue: Offline fallback page not showing

**Symptoms:**
- Network error instead of offline page
- Blank page offline

**Solutions:**

1. **Check fallback page exists:**
```bash
curl -k https://localhost/offline
# Should return HTML
```

2. **Check fallback is cached:**
   - DevTools → Application → Cache Storage
   - Look for /offline in cache

3. **Check fetch event handles failures:**
```javascript
self.addEventListener('fetch', (event) => {
    event.respondWith(
        fetch(event.request).catch(() => {
            return caches.match('/offline');  // Fallback
        })
    );
});
```

4. **Clear caches and recache:**
   - Clear all caches
   - Unregister SW
   - Reload (online)
   - Verify /offline is cached
   - Go offline and test

---

## Background Sync Issues

### Issue: Background Sync not registering

**Symptoms:**
- No sync events in DevTools
- "registration.sync.register is not a function"

**Solutions:**

1. **Check browser support:**
```javascript
// In console:
if ('sync' in ServiceWorkerRegistration.prototype) {
    console.log('Background Sync supported');
} else {
    console.log('Background Sync NOT supported');
}
```

**Browser Support:**
- Chrome/Edge: ✅
- Firefox: ❌ (not supported)
- Safari: ❌ (not supported)
- Opera: ✅

2. **Provide fallback for unsupported browsers:**
```javascript
if ('sync' in ServiceWorkerRegistration.prototype) {
    // Register sync
    await registration.sync.register('sync-entries');
} else {
    // Fallback: sync immediately
    await syncEntries();
}
```

3. **Check Service Worker is ready:**
```javascript
const registration = await navigator.serviceWorker.ready;
await registration.sync.register('sync-entries');
```

4. **Check sync permission:**
   - Some browsers require notification permission
   - Request notification permission first

---

### Issue: Sync event not firing

**Symptoms:**
- Sync registers but doesn't trigger
- No sync in Background Services

**Solutions:**

1. **Check DevTools Background Services:**
   - DevTools → Application → Background Services → Background Sync
   - Should see registered syncs
   - Check "Record" checkbox

2. **Check sync event listener:**
```javascript
// In sw.js:
self.addEventListener('sync', (event) => {
    if (event.tag === 'sync-entries') {
        event.waitUntil(syncPendingEntries());
    }
});
```

3. **Force sync:**
   - Network tab → Go online
   - Wait a few seconds
   - Or close and reopen browser

4. **Check sync timing:**
   - Sync may be delayed by browser
   - Browser controls when sync runs
   - May not fire immediately

5. **Test with network throttling, not full offline:**
   - Use "Slow 3G" instead of "Offline"
   - Or toggle online/offline quickly

---

### Issue: Sync data not persisting

**Symptoms:**
- Data lost after browser close
- LocalStorage empty

**Solutions:**

1. **Check localStorage quota:**
```javascript
// In console:
if (navigator.storage && navigator.storage.estimate) {
    navigator.storage.estimate().then(estimate => {
        console.log('Usage:', estimate.usage);
        console.log('Quota:', estimate.quota);
    });
}
```

2. **Check incognito mode:**
   - Storage may not persist in incognito
   - Test in normal browser window

3. **Check data is being saved:**
```javascript
// After saving:
console.log(localStorage.getItem('fieldlog_pending_entries'));
```

4. **Use IndexedDB for larger data:**
```javascript
// localStorage has ~5MB limit
// Use IndexedDB for more storage
```

5. **Check browser settings:**
   - Ensure "Clear on exit" is not enabled
   - Check privacy settings

---

## Push Notification Issues

### Issue: Permission request not appearing

**Symptoms:**
- requestPermission() returns 'denied' immediately
- No permission prompt shows

**Solutions:**

1. **Check browser support:**
```javascript
if ('Notification' in window) {
    console.log('Notifications supported');
} else {
    console.log('Notifications NOT supported');
}
```

2. **Check permission status:**
```javascript
console.log('Permission:', Notification.permission);
// Can be: 'default', 'granted', 'denied'
```

3. **Reset permission:**
   - Chrome: Click lock icon → Site settings → Permissions → Notifications → Reset
   - Firefox: Click lock icon → More information → Permissions → Receive Notifications → Clear
   - Or try incognito mode

4. **Request on user action:**
```javascript
// Must be called from user interaction (click)
button.addEventListener('click', async () => {
    const permission = await Notification.requestPermission();
    console.log('Permission:', permission);
});
```

5. **Check if already blocked:**
   - If user previously denied, can't request again
   - User must manually enable in settings

---

### Issue: Push subscription fails

**Symptoms:**
- pushManager.subscribe() throws error
- "Registration failed"

**Solutions:**

1. **Check Service Worker is active:**
```javascript
const registration = await navigator.serviceWorker.ready;
console.log('SW state:', registration.active.state);
```

2. **Check VAPID key format:**
```javascript
// Must be Uint8Array, not string:
function urlBase64ToUint8Array(base64String) {
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
```

3. **Check VAPID key is accessible:**
```javascript
const vapidPublicKey = document.querySelector('meta[name="vapid-public-key"]').content;
console.log('VAPID key:', vapidPublicKey);
```

4. **Check subscription options:**
```javascript
const subscription = await registration.pushManager.subscribe({
    userVisibleOnly: true,  // REQUIRED
    applicationServerKey: vapidKeyAsUint8Array
});
```

5. **Check browser console for errors:**
   - May show specific error message
   - Check for VAPID errors

---

### Issue: Notifications not appearing

**Symptoms:**
- No notification shows
- No error, but notification doesn't display

**Solutions:**

1. **Check notification permission:**
```javascript
if (Notification.permission !== 'granted') {
    console.log('Permission not granted');
}
```

2. **Check OS notification settings:**
   - Windows: Settings → System → Notifications
   - Mac: System Preferences → Notifications
   - Android: Settings → Apps → Chrome → Notifications

3. **Check Do Not Disturb mode:**
   - Disable Do Not Disturb
   - Check Focus Assist (Windows)

4. **Test with simple notification:**
```javascript
// Try basic notification first:
new Notification('Test', {
    body: 'This is a test'
});
```

5. **Check notification format:**
```javascript
registration.showNotification('Title', {
    body: 'Body text',
    icon: '/icons/icon-192.png',  // Absolute path
    badge: '/icons/badge.png',
    tag: 'unique-tag',
    requireInteraction: false
});
```

6. **Check SW notification handler:**
```javascript
// In sw.js:
self.addEventListener('push', (event) => {
    const data = event.data.json();
    event.waitUntil(
        self.registration.showNotification(data.title, {
            body: data.body,
            icon: data.icon
        })
    );
});
```

---

## Performance Issues

### Issue: App is slow to load

**Solutions:**

1. **Run Lighthouse audit:**
   - DevTools → Lighthouse → Performance
   - Identify bottlenecks

2. **Check asset sizes:**
```bash
ls -lh public/build/
# Look for large files
```

3. **Optimize images:**
```bash
# Use imagemin or similar
```

4. **Enable compression:**
   - Ensure gzip/brotli enabled on server
   - Check response headers: `Content-Encoding: gzip`

5. **Optimize caching:**
   - Cache static assets aggressively
   - Use CacheFirst for assets

6. **Lazy load images:**
```html
<img loading="lazy" src="image.jpg" alt="...">
```

---

### Issue: Service Worker causes slow performance

**Solutions:**

1. **Check SW isn't doing too much work:**
   - Don't cache everything
   - Limit cache sizes
   - Use max_entries and max_age

2. **Check for cache bloat:**
```javascript
// In console:
caches.keys().then(names => {
    names.forEach(name => {
        caches.open(name).then(cache => {
            cache.keys().then(keys => {
                console.log(name, 'has', keys.length, 'items');
            });
        });
    });
});
```

3. **Clean up old caches:**
```javascript
// In sw.js activate event:
const cacheWhitelist = ['v1-static', 'v1-images'];
event.waitUntil(
    caches.keys().then(names => {
        return Promise.all(
            names.map(name => {
                if (!cacheWhitelist.includes(name)) {
                    return caches.delete(name);
                }
            })
        );
    })
);
```

---

## Platform-Specific Issues

### iOS Safari Issues

**Issue: App not installing on iOS**

**Solutions:**
- iOS requires manual "Add to Home Screen"
- No automatic install prompt
- Show instructions to users

**Issue: Service Worker limitations on iOS**

**Known Limitations:**
- SW may be killed after 30 seconds of inactivity
- Background Sync not supported
- Push notifications limited
- Cache quota is smaller

**Workarounds:**
- Keep SW tasks fast
- Use lightweight caching
- Provide degraded experience on iOS

---

### Android Issues

**Issue: Icon doesn't adapt on Android**

**Solutions:**
1. Use maskable icon:
```yaml
icons:
    - src: '/icons/icon-maskable.png'
      sizes: '512x512'
      purpose: 'maskable'
```

2. Test at https://maskable.app/

3. Ensure safe zone:
   - Keep important parts in center 80%
   - Android will apply mask (circle, squircle, etc.)

---

## Emergency Troubleshooting

When all else fails:

### Nuclear Option: Complete Reset

```bash
# 1. Stop everything
castor stop

# 2. Clear Symfony cache
castor console cache:clear

# 3. Clear browser completely
# DevTools → Application → Clear storage → Clear site data

# 4. Unregister Service Workers
# In console:
navigator.serviceWorker.getRegistrations().then(r => r.forEach(reg => reg.unregister()))

# 5. Close browser completely

# 6. Restart Docker
castor destroy
castor build
castor start

# 7. Reinstall dependencies
castor install

# 8. Reopen browser in incognito

# 9. Test
```

---

## Getting Help During Workshop

### Self-Service Steps

1. Check this troubleshooting guide first
2. Check browser console for errors
3. Check DevTools Application tab
4. Ask your neighbor
5. Check official docs
6. Raise your hand for facilitator

### What to Provide When Asking for Help

- What you were trying to do
- What you expected to happen
- What actually happened
- Error messages (screenshot or copy)
- Browser and OS version
- Console errors

---

## Additional Resources

**Browser DevTools Docs:**
- [Chrome DevTools](https://developers.google.com/web/tools/chrome-devtools)
- [Firefox Developer Tools](https://developer.mozilla.org/en-US/docs/Tools)

**Service Worker Debugging:**
- [Debugging Service Workers](https://developers.google.com/web/fundamentals/codelabs/debugging-service-workers)

**Stack Overflow:**
- [PWA tag](https://stackoverflow.com/questions/tagged/progressive-web-apps)
- [Service Worker tag](https://stackoverflow.com/questions/tagged/service-worker)

---

## Known Issues & Workarounds

### Bundle-Specific Issues

**Issue: Bundle generates invalid Service Worker syntax**
- Workaround: Override with custom SW file

**Issue: Manifest not updating after config change**
- Workaround: Clear cache + restart: `castor console cache:clear && castor restart`

---

This troubleshooting guide should help you quickly resolve most issues during the workshop!
