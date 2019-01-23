import { NgModule } from '@angular/core';
import { SignInComponent } from './signin/signin.component';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { MatButtonModule, MatFormFieldModule, MatInputModule, MatCardModule, MatSnackBarModule } from '@angular/material';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { SignUpComponent } from './sigup/signup.component';
import { RouterModule } from '@angular/router';
import { SignUpService } from './sigup/signup.service';
import { AlertModule } from '../shared/services/alert/alert.module';



@NgModule({
    declarations: [
        SignInComponent,
        SignUpComponent
    ],
    imports: [
        BrowserAnimationsModule,
        MatFormFieldModule,
        MatInputModule,
        MatButtonModule,
        FormsModule,
        ReactiveFormsModule,
        MatCardModule,
        RouterModule,
        AlertModule
    ],
    providers: [
        SignUpService
    ]
})

export class HomeModule { }
