import { NgModule } from '@angular/core';
import { RegisterStudentComponent } from './register-student.component';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormsModule } from '@angular/forms';
import { MatCardModule, MatButtonModule, MatExpansionModule,
        MatDatepickerModule, MatFormFieldModule, MatIconModule,
        MatNativeDateModule, MatInputModule, MatRadioModule, MatCheckboxModule, MatDialogModule, MatSelectModule } from '@angular/material';
import { TextMaskModule } from 'angular2-text-mask';
import { RegisterStudentService } from './register-student.service';
import { ContractComponent } from './contract/contract.component';
import { RegisterImageComponent } from './register-image/register-image.component';
import { YesOrNoPipe } from '../shared/pipes/yes-or-no.pipe';
import { MonthPTPipe } from '../shared/pipes/monthPT.pipe';
import { AlertModule } from '../shared/services/alert/alert.module';


@NgModule({
    declarations: [
        RegisterStudentComponent,
        ContractComponent,
        RegisterImageComponent,
        YesOrNoPipe,
        MonthPTPipe
    ],
    entryComponents: [RegisterImageComponent],
    imports: [
        CommonModule,
        MatCardModule,
        MatButtonModule,
        MatExpansionModule,
        MatDatepickerModule,
        MatFormFieldModule,
        MatInputModule,
        MatIconModule,
        MatNativeDateModule,
        MatRadioModule,
        MatCheckboxModule,
        MatDialogModule,
        MatSelectModule,
        ReactiveFormsModule,
        FormsModule,
        TextMaskModule,
        AlertModule
    ],
    providers: [
        RegisterStudentService
    ]
})

export class RegisterModule { }
