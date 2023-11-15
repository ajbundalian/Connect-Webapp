document.addEventListener('DOMContentLoaded', function () {
    var saveButtons = document.querySelectorAll('.btn-save-btn');
    saveButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            var jobId = this.getAttribute('data-job-id');
            saveUnsaveJob(jobId, this);
        });
    });
});

function saveUnsaveJob(jobId, button) {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'job-save-ajax.php', true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.onload = function () {
        if (this.responseText.includes("saved")) {
            button.innerHTML = 'Unsave';
            button.disabled = false;
        } else if (this.responseText.includes("unsaved")) {
            button.innerHTML = 'Save';
            button.disabled = false;
        }
        console.log(this.responseText);
    };
    console.log(jobId)
    xhr.send('job_id=' + encodeURIComponent(jobId));
    button.disabled = true;
}

document.addEventListener('DOMContentLoaded', function () {
    var connectButtons = document.querySelectorAll('.btn-connect-btn');
    connectButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            if (!this.disabled) {
                var jobId = this.getAttribute('data-job-id');
                connectToJob(jobId, this);
            }
        });
    });
});

function connectToJob(jobId, button) {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'job-connect-ajax.php', true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.onload = function () {
        if (this.responseText.includes("Connected")) {
            button.innerHTML = 'Connected';
            button.disabled = true;
        }
        console.log(this.responseText);
    };
    xhr.send('job_id=' + encodeURIComponent(jobId) + '&ajax=1');
}

document.addEventListener('DOMContentLoaded', function() {
    var reportButton = document.querySelector('.btn-report-btn');
    
    reportButton.addEventListener('click', function() {
        var jobId = this.getAttribute('data-job-id');
        window.location.href = 'report-job-form.php?job_id=' + jobId;
    });
});