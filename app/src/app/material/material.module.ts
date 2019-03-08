import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { MaterialComponent } from './material.component';
import { ManageMaterialComponent } from './manage-material/manage-material.component';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { MatCardModule, MatButtonModule, MatInputModule, MatFormFieldModule,
     MatSelectModule, MatIconModule, MatTabsModule } from '@angular/material';
import { DragDropModule } from '@angular/cdk/drag-drop';
import { InputFileConfig, InputFileModule } from 'ngx-input-file';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';

const config: InputFileConfig = {};

@NgModule({
    declarations: [
        MaterialComponent,
        ManageMaterialComponent
    ],
    imports: [
        CommonModule,
        FormsModule,
        ReactiveFormsModule,
        BrowserAnimationsModule,
        InputFileModule.forRoot(config),
        MatCardModule,
        MatButtonModule,
        MatInputModule,
        MatFormFieldModule,
        MatSelectModule,
        MatIconModule,
        MatTabsModule,
        DragDropModule
    ]
})

export class MaterialModule {}
