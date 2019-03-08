import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from 'src/environments/environment';
import { tap } from 'rxjs/operators';
import { UserService } from 'src/app/user/user.service';
import { ResponseApi } from '../response-api';
import { AlertService } from 'src/app/shared/services/alert/alert.service';
import { SystemVersion } from './systemVersion';

@Injectable({
    providedIn: 'root'
})
export class AuthService {

    API = environment.ApiUrl;

    constructor(
        private http: HttpClient,
        private userService: UserService,
        private alertService: AlertService) {}

    authenticate(email: string, password: string) {

        return this.http
            .post(this.API + '/access/signin', {email, password}, {observe: 'response'})
            .pipe(
                tap( res => {
                    const response = res.body as ResponseApi;
                    if (! response.error) {
                        this.userService.setUserLoggedData(response.data);
                        this.setVersionSystem();
                    }
            }));
    }

    isUpdated() {

        return this.http.get(this.API + '/system/version', {observe: 'response'});
    }

    setVersionSystem() {
        this.http.get(this.API + '/system/version', {observe: 'response'})
            .subscribe( res => {
                const response = res.body as ResponseApi;

                if (!response.error) {

                    const version = response.data as SystemVersion;
                    window.localStorage.setItem(btoa('System Version'), btoa(JSON.stringify(version)));
                    console.log('Current version: ', version.version, ' - build date:', version.buildDate);
                }
            });

    }

    getCurrentVersionLoged(): SystemVersion {
        const versionRaw = window.localStorage.getItem(btoa('System Version'));
        if (versionRaw) {

            const localVersion: SystemVersion = JSON.parse(atob(versionRaw));
            return localVersion;
        }

        const empty: SystemVersion = {buildDate: '0', version: '0'};
        return empty;
    }
}
