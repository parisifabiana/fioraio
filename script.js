//script.js 
document.addEventListener('DOMContentLoaded', function() {
    fetch('header.html')
        .then(response => response.text())
        .then(data => {
            document.body.insertAdjacentHTML('afterbegin', data);
        });

    fetch('footer.html')
        .then(response => response.text())
        .then(data => {
            document.body.insertAdjacentHTML('beforeend', data);
        });
});


document.addEventListener('DOMContentLoaded', function() {
    let slider = tns({
        container: '.slider',
        items: 1,
        slideBy: 'page',
        autoplay: true,
        controls: false,
        nav: false,
        autoplayButtonOutput: false
    });
});

document.getElementById('contact-form').addEventListener('submit', function(event) {
    var name = document.getElementById('name').value;
    var email = document.getElementById('email').value;
    var subject = document.getElementById('subject').value;
    var message = document.getElementById('message').value;
    var errors = [];
  
    if (name.trim() === '') {
      errors.push('Il campo Nome è obbligatorio');
    }
  
    if (email.trim() === '') {
      errors.push('Il campo Email è obbligatorio');
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
      errors.push('Inserisci un indirizzo email valido');
    }
  
    if (subject.trim() === '') {
      errors.push('Il campo Oggetto è obbligatorio');
    }
  
    if (message.trim() === '') {
      errors.push('Il campo Messaggio è obbligatorio');
    }
  
    if (errors.length > 0) {
      event.preventDefault();
      alert('Si sono verificati i seguenti errori:\n' + errors.join('\n'));
    }
  });