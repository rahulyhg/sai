import { Component, Inject, Optional, ViewChild, ElementRef, AfterViewInit, OnDestroy } from '@angular/core';
import { MatDialogRef, MAT_DIALOG_DATA } from '@angular/material';

@Component({
    templateUrl: './register-image.component.html'
})

export class RegisterImageComponent implements AfterViewInit, OnDestroy {

    @ViewChild('video') public video: ElementRef;
    @ViewChild('canvas') public canvas: ElementRef;
    @ViewChild('captureButton', {read: ElementRef}) public captureButton: ElementRef;
    @ViewChild('recaptureButton', {read: ElementRef}) public recaptureButton: ElementRef;
    @ViewChild('saveButton', {read: ElementRef}) public saveButton: ElementRef;
    @ViewChild('borderVideo') public borderVideo: ElementRef;
    private stream: MediaStream;


    constructor(
        @Optional() public dialogRef: MatDialogRef<RegisterImageComponent>,
        @Optional() @Inject(MAT_DIALOG_DATA) public data) {}

    public ngAfterViewInit() {
        this.canvas.nativeElement.style.display = 'none';
        this.recaptureButton.nativeElement.style.display = 'none';
        this.saveButton.nativeElement.style.display = 'none';

        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            navigator.mediaDevices.getUserMedia({ video: true }).then(stream => {
                this.stream = stream;
                this.video.nativeElement.srcObject = stream;
                this.video.nativeElement.play();
            });
        }
    }

    ngOnDestroy(): void {
        if (this.stream) { this.stream.getTracks()[0].stop(); }
    }

    public capture() {
        this.video.nativeElement.style.display = 'none';
        this.captureButton.nativeElement.style.display = 'none';
        this.borderVideo.nativeElement.style.display = 'none';
        this.canvas.nativeElement.style.display = '';
        this.saveButton.nativeElement.style.display = '';
        this.recaptureButton.nativeElement.style.display = '';
        this.canvas.nativeElement.getContext('2d')
            .drawImage(this.video.nativeElement, 150, 0, 354, 472, 0, 0, 354, 472);
        this.data = this.canvas.nativeElement.toDataURL('image/png');
    }

    public recapture() {
        this.saveButton.nativeElement.style.display = 'none';
        this.recaptureButton.nativeElement.style.display = 'none';
        this.canvas.nativeElement.style.display = 'none';
        this.captureButton.nativeElement.style.display = '';
        this.video.nativeElement.style.display = '';
        this.borderVideo.nativeElement.style.display = '';
    }

    public save() {
        this.stream.getTracks()[0].stop();
    }


}
