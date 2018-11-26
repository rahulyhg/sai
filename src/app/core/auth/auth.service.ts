import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from 'src/environments/environment';
import { tap } from 'rxjs/operators';
import { UserService } from 'src/app/user/user.service';
import { ResponseApi } from '../response-api';

@Injectable({
    providedIn: 'root'
})
export class AuthService {

    API = environment.ApiUrl;

    constructor(
        private http: HttpClient,
        private userService: UserService) {}

    authenticate(email: string, password: string) {

        return this.http
            .post(this.API + '/access/signin', {email, password}, {observe: 'response'}
            ).pipe(tap(res => {
                const response = res.body as ResponseApi;
                if (! response.error) {
                    this.userService.setUserLoggedData(response.data);
                    console.log(`User ${email} authenticated`);
                }
                console.log('authService.autenticate res', response);
            }));
    }
}
