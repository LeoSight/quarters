import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static values = {
        interval: Number
    }

    initial;
    seconds;

    connect() {
        this.seconds = this.intervalValue;
        this.initial = this.element.textContent;

        if(this.seconds > 0) {
            this.display();
            this.timer = setInterval(() => {
                this.seconds -= 1;
                this.display();
            }, 1000);
        }
    }

    display() {
        if(this.seconds < 0) {
            //this.element.textContent = this.initial;
            clearInterval(this.timer);
            window.location.reload();
            return;
        }

        let h = Math.floor(this.seconds / 3600);
        let m = Math.floor((this.seconds - h * 3600) / 60);
        let s = this.seconds - h * 3600 - m * 60;

        if(h > 0){
            this.element.innerHTML = `${this.initial} (<strong>${String(h).padStart(2, '0')}:<strong>${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}</strong>)`;
        }else{
            this.element.innerHTML = `${this.initial} (<strong>${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}</strong>)`;
        }
    }
}
