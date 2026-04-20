import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Alpine Store for Import Modal - Initialize before starting Alpine
Alpine.store('importModal', {
    show: false,
    toggle() {
        this.show = !this.show;
    },
    open() {
        console.log('Opening import modal...');
        this.show = true;
    },
    close() {
        console.log('Closing import modal...');
        this.show = false;
    }
});

Alpine.store('whatsapp', {
    templates: {
        activation: 'Hola {cliente},\n\nSu servicio asociado al dominio *{dominio}* ha sido activado exitosamente.\n\n¡Gracias por confiar en nosotros!',
        expiry: 'Hola {cliente},\n\nLe recordamos que su dominio *{dominio}* está próximo a vencer.\n\nPor favor, confirme si desea renovarlo para evitar interrupciones.\n\nQuedamos atentos.',
        promo: 'Estimado/a {cliente},\n\nLe informamos que tiene un saldo pendiente para la renovación de *{dominio}*.\n\nPor favor, envíenos su comprobante una vez realizado el pago.\n\n¡Gracias!'
    },
    sendDirect(phone, domainName, clientName, templateKey) {
        if (!phone) {
            alert('Este dominio no tiene un número de teléfono registrado.');
            return;
        }
        let message = this.templates[templateKey] || '';
        message = message.replace('{cliente}', clientName || 'Cliente');
        message = message.replace('{dominio}', domainName);
        
        const encodedMessage = encodeURIComponent(message);
        const cleanPhone = phone.replace(/\D/g, '');
        window.open(`https://wa.me/${cleanPhone}?text=${encodedMessage}`, '_blank');
    }
});

Alpine.start();

// Log to confirm Alpine and store are loaded
console.log('Alpine started with store:', Alpine.store('importModal'));
