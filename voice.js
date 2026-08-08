// ====================================
// ভয়েস রিকগনিশন (Web Speech API)
// ====================================

var recognition = null;
var isListening = false;

// ব্রাউজার সাপোর্ট চেক
if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
    var SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    recognition = new SpeechRecognition();
    recognition.lang = 'bn-BD';
    recognition.continuous = false;
    recognition.interimResults = false;
    recognition.maxAlternatives = 1;
} else {
    console.log('ভয়েস সাপোর্ট নেই');
}

// ভয়েস রিকগনিশন শুরু
function startVoiceInput() {
    var voiceBtn = document.getElementById('voiceBtn');
    if (!voiceBtn) return; // যদি বাটন না থাকে, তাহলে কিছু করো না
    
    if (!recognition) {
        alert('দুঃখিত! আপনার ব্রাউজার ভয়েস সাপোর্ট করে না। দয়া করে Chrome বা Edge ব্যবহার করুন।');
        return;
    }
    
    if (isListening) {
        recognition.stop();
        return;
    }
    
    voiceBtn.innerHTML = '⏹️ শুনছি...';
    voiceBtn.classList.add('listening');
    document.getElementById('voiceStatus').innerHTML = '🎤 এখন কথা বলুন...';
    document.getElementById('voiceStatus').style.color = '#00e676';
    
    recognition.start();
    isListening = true;
}

// রেজাল্ট পাওয়া গেলে
recognition.onresult = function(event) {
    var transcript = event.results[0][0].transcript;
    console.log('শোনা গেল:', transcript);
    sendVoiceData(transcript);
};

// এরর হলে
recognition.onerror = function(event) {
    console.error('ভয়েস এরর:', event.error);
    var statusEl = document.getElementById('voiceStatus');
    if (statusEl) {
        if (event.error === 'not-allowed') {
            statusEl.innerHTML = '❌ মাইক্রোফোন অ্যাক্সেস অস্বীকৃত!';
        } else {
            statusEl.innerHTML = '❌ কিছু সমস্যা হয়েছে। আবার চেষ্টা করুন।';
        }
    }
    resetVoiceButton();
};

// শেষ হলে
recognition.onend = function() {
    resetVoiceButton();
};

function resetVoiceButton() {
    var voiceBtn = document.getElementById('voiceBtn');
    if (voiceBtn) {
        voiceBtn.innerHTML = '🎤 কথা বলে যোগ করুন';
        voiceBtn.classList.remove('listening');
    }
    var statusEl = document.getElementById('voiceStatus');
    if (statusEl) {
        statusEl.innerHTML = '⏸️ প্রস্তুত';
        statusEl.style.color = '#8899bb';
    }
    isListening = false;
}

// ====================================
// ভয়েস ডেটা PHP-তে পাঠানো (AJAX)
// ====================================
function sendVoiceData(transcript) {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'index.php?action=voiceAdd', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    
    xhr.onload = function() {
        if (this.status == 200) {
            var response = JSON.parse(this.responseText);
            var statusEl = document.getElementById('voiceStatus');
            
            if (response.success) {
                if (statusEl) {
                    statusEl.innerHTML = '✅ ' + response.message;
                    statusEl.style.color = '#00e676';
                }
                
                if (typeof loadPendingList === 'function') {
                    loadPendingList();
                }
                
                if (response.items && response.items.length > 0) {
                    var msg = '✅ যোগ হয়েছে: ';
                    response.items.forEach(function(item) {
                        msg += item + ', ';
                    });
                    if (statusEl) {
                        statusEl.innerHTML = msg.slice(0, -2);
                    }
                }
            } else {
                if (statusEl) {
                    statusEl.innerHTML = '❌ ' + response.message;
                    statusEl.style.color = '#ff1744';
                }
            }
        } else {
            var statusEl = document.getElementById('voiceStatus');
            if (statusEl) {
                statusEl.innerHTML = '❌ সার্ভার সমস্যা!';
            }
        }
    };
    
    xhr.send('voice_text=' + encodeURIComponent(transcript));
}