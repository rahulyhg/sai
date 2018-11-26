import { NgModule } from '@angular/core';
import { RegisterStudentComponent } from './register-student.component';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormsModule } from '@angular/forms';
import { MatCardModule, MatButtonModule, MatExpansionModule,
        MatDatepickerModule, MatFormFieldModule, MatIconModule,
        MatNativeDateModule, MatInputModule } from '@angular/material';

@NgModule({
    declarations: [RegisterStudentComponent],
    imports: [
        CommonModule,
        ReactiveFormsModule,
        MatCardModule,
        MatButtonModule,
        MatExpansionModule,
        MatDatepickerModule,
        MatFormFieldModule,
        MatInputModule,
        MatIconModule,
        MatNativeDateModule,
        FormsModule
    ]
})

export class RegisterModule { }
