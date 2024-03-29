import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { LayoutModule } from '@angular/cdk/layout';
import {
    MatSidenavModule,
    MatButtonModule,
    MatToolbarModule,
    MatIconModule,
    MatListModule,
    MatMenuModule,
    MatDividerModule
} from '@angular/material';

import { MenuComponent } from './menu.component';
import { RouterModule } from '@angular/router';
import { LoadingModule } from '../shared/components/loading/loading.module';

@NgModule({
    declarations: [
        MenuComponent
    ],
    imports: [
        CommonModule,
        FormsModule,
        MatSidenavModule,
        MatButtonModule,
        LayoutModule,
        MatToolbarModule,
        MatIconModule,
        MatListModule,
        MatMenuModule,
        RouterModule,
        LoadingModule,
        MatDividerModule
    ],
    exports: [MenuComponent]
})

export class MenuModule { }
