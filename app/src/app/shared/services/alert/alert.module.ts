import { NgModule } from '@angular/core';
import { AlertService } from './alert.service';
import { MatSnackBarModule } from '@angular/material';

@NgModule({
    providers: [AlertService],
    imports: [MatSnackBarModule]
})

export class AlertModule { }
