document.addEventListener('DOMContentLoaded', function() {
    createParticles();
    addMouseParallax();
    addButtonEffects();
});

function createParticles() {
    const container = document.querySelector('.floating-houses');
    const houseEmojis = ['🏠', '🏡', '🏘️', '🏚️', '🏗️'];
    
    for (let i = 0; i < 5; i++) {
        setTimeout(() => {
            const particle = document.createElement('div');
            particle.className = 'floating-house';
            particle.textContent = houseEmojis[Math.floor(Math.random() * houseEmojis.length)];
            particle.style.left = Math.random() * 100 + '%';
            particle.style.top = Math.random() * 100 + '%';
            particle.style.animationDelay = Math.random() * -15 + 's';
            particle.style.fontSize = (30 + Math.random() * 20) + 'px';
            container.appendChild(particle);
        }, i * 500);
    }
}

function addMouseParallax() {
    const houseIcon = document.querySelector('.house-icon');
    
    document.addEventListener('mousemove', function(e) {
        const x = (window.innerWidth / 2 - e.clientX) / 50;
        const y = (window.innerHeight / 2 - e.clientY) / 50;
        
        houseIcon.style.transform = `translate(${x}px, ${y}px)`;
    });
}

function addButtonEffects() {
    const button = document.querySelector('.home-button');
    
    button.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-5px) scale(1.05)';
    });
    
    button.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0) scale(1)';
    });
    
    button.addEventListener('click', function(e) {
        const ripple = document.createElement('span');
        ripple.style.cssText = `
            position: absolute;
            background: rgba(102, 126, 234, 0.3);
            border-radius: 50%;
            transform: scale(0);
            animation: ripple 0.6s linear;
            pointer-events: none;
        `;
        
        const rect = this.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        ripple.style.width = ripple.style.height = size + 'px';
        ripple.style.left = (e.clientX - rect.left - size/2) + 'px';
        ripple.style.top = (e.clientY - rect.top - size/2) + 'px';
        
        this.style.position = 'relative';
        this.style.overflow = 'hidden';
        this.appendChild(ripple);
        
        setTimeout(() => ripple.remove(), 600);
    });
    
    const style = document.createElement('style');
    style.textContent = `
        @keyframes ripple {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(style);
}

console.log('HomeRental 404 Page - Loaded Successfully');
