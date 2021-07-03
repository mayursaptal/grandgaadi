/* eslint-disable @typescript-eslint/naming-convention */
/* eslint-disable guard-for-in */
import { Injectable } from '@angular/core';
import { environment } from 'src/environments/environment';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { LoadingController, ToastController } from '@ionic/angular';
@Injectable({
  providedIn: 'root',
})
export class ApiService {
  apikey: any;
  baseUrl: any = '';
  wpcargoUrl: any = '';
  loading: HTMLIonLoadingElement;

  public shipments: any[];

  constructor(
    public loadingController: LoadingController,
    public toastController: ToastController,
    private http: HttpClient
  ) {
    this.baseUrl = environment.baseUrl;
    this.wpcargoUrl = environment.wpcargoUrl;
  }
  JSON_to_URLEncoded(element, key?, list?) {
    const new_list = list || [];
    if (typeof element === 'object') {
      for (const idx in element) {
        this.JSON_to_URLEncoded(
          element[idx],
          key ? key + '[' + idx + ']' : idx,
          new_list
        );
      }
    } else {
      new_list.push(key + '=' + encodeURIComponent(element));
    }
    return new_list.join('&');
  }

  async loaderShow() {


    this.loading = await this.loadingController.create({
      message: 'Please wait...',
      duration: 1000,
    });
    await this.loading.present();

  }

  async loaderhide() {
    if (this.loading) {
      await this.loading.onDidDismiss();
      this.loading = null;
    }
  }

  async toastMsg(msg) {
    const toast = await this.toastController.create({
      message: msg,
      duration: 1000,
    });
    toast.present();
  }

  post(url, body, is_form_data = false) {
    this.loaderShow();
    let header = {
      headers: new HttpHeaders().set(
        'Content-Type',
        'application/x-www-form-urlencoded'
      ),
    };
    let param = this.JSON_to_URLEncoded(body);
    if (is_form_data) {
      param = body;
      const data = this.http.post(this.wpcargoUrl + url, param);
      return data;
    }
    const data = this.http.post(this.wpcargoUrl + url, param, header);
    return data;
  }

  get(url) {
    const header = {
      headers: new HttpHeaders().set(
        'Content-Type',
        'application/x-www-form-urlencoded'
      ),
    };
    // this.loaderShow();
    return this.http.get(this.wpcargoUrl + url, header);
  }


}
