import { Injectable } from '@angular/core';
import { MatSnackBar, MatSnackBarConfig } from '@angular/material';

@Injectable({
    providedIn: 'root'
})

export class AlertService {

    constructor(private alert: MatSnackBar) {}

    error(msg: string, duration?: number) {
        const config = new MatSnackBarConfig();
        config.panelClass = ['bg-color-warn'];
        duration ? config.duration = duration : config.duration = 15000;
        config.horizontalPosition = 'right';
        this.alert.open(msg, 'ok', config);
    }

    success(msg: string, duration?: number) {
        const config = new MatSnackBarConfig();
        config.panelClass = ['bg-color-primary'];
        duration ? config.duration = duration : config.duration = 15000;
        config.horizontalPosition = 'right';
        this.alert.open(msg, 'ok', config);
    }

    warning(msg: string, duration?: number) {
        const config = new MatSnackBarConfig();
        config.panelClass = ['bg-color-accent'];
        duration ? config.duration = duration : config.duration = 15000;
        config.horizontalPosition = 'right';
        this.alert.open(msg, 'ok', config);
    }
}
