// Main JavaScript file for Booking System

// Add active class to current navigation item
document.addEventListener('DOMContentLoaded', function() {
    const currentLocation = location.href;
    const menuItems = document.querySelectorAll('.nav-menu a');
    
    menuItems.forEach(item => {
        if(item.href === currentLocation) {
            item.classList.add('active');
        }
    });
});

// Form validation helper
function validateForm(formId) {
    const form = document.getElementById(formId);
    const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
    let isValid = true;

    inputs.forEach(input => {
        if(!input.value.trim()) {
            input.style.borderColor = 'red';
            isValid = false;
        } else {
            input.style.borderColor = '#ddd';
        }
    });

    return isValid;
}

// Show alert message
function showAlert(message, type = 'success') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.textContent = message;
    
    const container = document.querySelector('.container');
    container.insertBefore(alertDiv, container.firstChild);
    
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}

console.log('ADBook initialized');
