import { NgModule } from '@angular/core';
import { RegisterStudentComponent } from './register-student.component';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormsModule } from '@angular/forms';
import { MatCardModule, MatButtonModule, MatExpansionModule,
        MatDatepickerModule, MatFormFieldModule, MatIconModule,
        MatNativeDateModule, MatInputModule, MatRadioModule, MatCheckboxModule } from '@angular/material';
import { TextMaskModule } from 'angular2-text-mask';
import { RegisterStudentService } from './register-student.service';
import { ContractComponent } from './contract/contract.component';

@NgModule({
    declarations: [
        RegisterStudentComponent,
        ContractComponent
    ],
    imports: [
        CommonModule,
        ReactiveFormsModule,
        FormsModule,
        MatCardModule,
        MatButtonModule,
        MatExpansionModule,
        MatDatepickerModule,
        MatFormFieldModule,
        MatInputModule,
        MatIconModule,
        MatNativeDateModule,
        TextMaskModule,
        MatRadioModule,
        MatCheckboxModule
    ],
    providers: [
        RegisterStudentService
    ]
})

export class RegisterModule { }
