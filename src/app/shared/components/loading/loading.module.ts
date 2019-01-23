import { NgModule } from '@angular/core';
import { LoadingComponent } from './loading.component';
import { MatProgressBarModule } from '@angular/material';
import { CommonModule } from '@angular/common';
import { HTTP_INTERCEPTORS } from '@angular/common/http';
import { LoadingInterceptor } from './loading.interceptor';

@NgModule({
    declarations: [LoadingComponent],
    exports: [LoadingComponent],
    imports: [CommonModule, MatProgressBarModule],
    providers: [{
        provide: HTTP_INTERCEPTORS,
        useClass: LoadingInterceptor,
        multi: true
    }]
})

export class LoadingModule {}
