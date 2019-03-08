import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { RegisterPresenceComponent } from './register-presence/register-presence.component';
import { MatCardModule, MatButtonModule, MatSelectModule, MatRadioModule,
    MatTableModule, MatTabsModule, MatInputModule } from '@angular/material';

@NgModule({
    declarations: [
        RegisterPresenceComponent
    ],
    imports: [
        CommonModule,
        FormsModule,
        ReactiveFormsModule,
        MatCardModule,
        MatSelectModule,
        MatButtonModule,
        MatRadioModule,
        MatTableModule,
        MatTabsModule,
        MatInputModule
    ]
})

export class PresenceModule {}
