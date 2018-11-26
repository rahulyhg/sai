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
    MatMenuModule
} from '@angular/material';

import { MenuComponent } from './menu.component';
import { RouterModule } from '@angular/router';

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
        RouterModule
    ],
    exports: [MenuComponent]
})

export class MenuModule { }
