
importScripts('https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.10.1/firebase-messaging.js');

self.addEventListener('message', event => {
    if (event.data && event.data.type === 'SETUP') {
        if (!firebase.apps.length) {
            firebase.initializeApp(event.data.config);
        }
        const messaging = firebase.messaging();

        messaging.setBackgroundMessageHandler(function (payload) {
            console.log("Background message received.", payload);

            const notificationTitle = payload.data.title;
            const notificationOptions = {
                body: payload.data.body,
                icon: payload.data.icon || '/icon.png',
                badge: payload.data.badge || '/badge.png',
                data: {
                    link: payload.data?.link || null
                }
            };

            return self.registration.showNotification(notificationTitle, notificationOptions);
        });
    }
});
self.addEventListener('activate', event => {
    event.waitUntil(self.clients.claim());
});

self.addEventListener('push', function (event) {
    const messageData = event.data.json();
    console.log('Push message received:', messageData);

    const notificationOptions = {
        body: messageData.data.body,
        icon: messageData.data.icon || '/icon.png',
        badge: messageData.data.badge || '/badge.png',
        data: {
            link: messageData.data?.link || null
        }
    };

    console.log('Notification options:', notificationOptions);

    // Gửi message đến các tab đang mở
    self.clients.matchAll({ type: "window", includeUncontrolled: true }).then(clients => {
        clients.forEach(client => {
            client.postMessage({
                type: "push-notification",
                payload: messageData
            });
        });
    });

    event.waitUntil(
        self.registration.showNotification(messageData.data.title, notificationOptions)
    );
});


self.addEventListener('notificationclick', function (event) {
    console.log('On notification click: ', event.notification.tag);
    event.notification.close();

    // Get link from the push message data
    const payload = event.notification.data;
    const link = payload?.link || null;

    event.waitUntil(
        clients.matchAll({
            type: "window"
        }).then(function (clientList) {
            // If there's a specific link, open it
            if (link) {
                for (var i = 0; i < clientList.length; i++) {
                    var client = clientList[i];
                    if (client.url === link && 'focus' in client)
                        return client.focus();
                }
                if (clients.openWindow)
                    return clients.openWindow(link);
            } else {
                // Default behavior - open notifications page
                for (var i = 0; i < clientList.length; i++) {
                    var client = clientList[i];
                    if (client.url === self.location.origin + '/admin/dashboard' && 'focus' in client)
                        return client.focus();
                }
                if (clients.openWindow)
                    return clients.openWindow(self.location.origin + '/admin/dashboard');
            }
        })
    );
});
