@extends('admin.layouts.master')
@section('content')
<div class="content">
    <div class="animated fadeIn">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <strong>Create category</strong>
                    </div>
                    <div class="card-body card-block">
                        <form action="{{ route('.adminvouchers.store') }}" method="post" enctype="multipart/form-data" class="form-horizontal">
                            @csrf
                            <div class="row form-group">
                                <div class="col col-md-3">
                                    <label for="name" class=" form-control-label">Name</label>
                                </div>
                                <div class="col-12 col-md-9">
                                    <input type="text" value="{{ old('name') }}" id="name" name="name" placeholder="Name" class="form-control">
                                    <small class="help-block form-text text-danger">
                                        @if ($errors->has('name'))
                                            {{ $errors->first('name') }}
                                        @endif  
                                    </small>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col col-md-3">
                                    <label for="code" class=" form-control-label">Code</label>
                                </div>
                                <div class="col-12 col-md-9">
                                    <input type="text" value="{{ old('code') }}" id="code" name="code" placeholder="Code" class="form-control">
                                    <small class="help-block form-text text-danger">
                                        @if ($errors->has('code'))
                                            {{ $errors->first('code') }}
                                        @endif  
                                    </small>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col col-md-3">
                                    <label for="description" class=" form-control-label">Description</label>
                                </div>
                                <div class="col-12 col-md-9">
                                    <textarea name="description" id="description" cols="30" placeholder="Description" rows="4" class="form-control">{{ old('description') }}</textarea>
                                    <small class="help-block form-text text-danger">
                                        @if ($errors->has('description'))
                                            {{ $errors->first('description') }}
                                        @endif  
                                    </small>
                                </div>
                            </div>

                            <div class="row form-group">
                                <div class="col col-md-3">
                                    <label for="type" class=" form-control-label">Type</label>
                                </div>
                                <div class="col-12 col-md-9">
                                    <select name="type" id="type" class="form-control">
                                        <option value="">Select type voucher</option>
                                        <option {{ old('type') == 'Percent' ? 'selected' : '' }} value="Percent">Percent</option>
                                        <option {{ old('type') == 'Fixed' ? 'selected' : '' }} value="Fixed">Fixed</option>
                                    </select>
                                    <small class="help-block form-text text-danger">
                                        @if ($errors->has('type'))
                                            {{ $errors->first('type') }}
                                        @endif  
                                    </small>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col col-md-3">
                                    <label for="discount" class=" form-control-label">Discount</label>
                                </div>
                                <div class="col-12 col-md-9">
                                    <input type="text" value="{{ old('discount') }}" id="discount" name="discount" placeholder="Discount" class="form-control">
                                    <small class="help-block form-text text-danger">
                                        @if ($errors->has('discount'))
                                            {{ $errors->first('discount') }}
                                        @endif  
                                    </small>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col col-md-3">
                                    <label for="count" class=" form-control-label">Count</label>
                                </div>
                                <div class="col-12 col-md-9">
                                    <input type="number" min="0" value="{{ old('count') }}" id="count" name="count" placeholder="Count" class="form-control">
                                    <small class="help-block form-text text-danger">
                                        @if ($errors->has('count'))
                                            {{ $errors->first('count') }}
                                        @endif  
                                    </small>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col col-md-3">
                                    <label for="start-time" class=" form-control-label">Start time</label>
                                </div>
                                <div class="col-12 col-md-9">
                                    <input type="datetime-local" value="{{ old('start_time') }}" id="start-time" name="start_time" placeholder="Start time" class="form-control">
                                    <small class="help-block form-text text-danger">
                                        @if ($errors->has('start_time'))
                                        {{ $errors->first('start_time') }}
                                        @endif  
                                    </small>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col col-md-3">
                                    <label for="end-time" class=" form-control-label">End time</label>
                                </div>
                                <div class="col-12 col-md-9">
                                    <input type="datetime-local" id="end-time" value="{{ old('end_time') }}" name="end_time" placeholder="End time" class="form-control">
                                    <small class="help-block form-text text-danger">
                                        @if ($errors->has('end_time'))
                                            {{ $errors->first('end_time') }}
                                        @endif  
                                    </small>
                                </div>
                            </div>
                            
                            
                            <div class="row form-group">
                                <div class="col col-md-3">
                                    <label for="is-active" class=" form-control-label">Is active</label>
                                </div>
                                <div class="col-12 col-md-9">
                                    <label class="switch">
                                        <input {{ old('is_active') == 1 ? 'checked' : '' }} name="is_active" value="1" type="checkbox">
                                        <div class="slider">
                                            <div class="circle">
                                                <svg class="cross" xml:space="preserve" style="enable-background:new 0 0 512 512" viewBox="0 0 365.696 365.696" y="0" x="0" height="6" width="6" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" xmlns="http://www.w3.org/2000/svg">
                                                    <g>
                                                        <path data-original="#000000" fill="currentColor" d="M243.188 182.86 356.32 69.726c12.5-12.5 12.5-32.766 0-45.247L341.238 9.398c-12.504-12.503-32.77-12.503-45.25 0L182.86 122.528 69.727 9.374c-12.5-12.5-32.766-12.5-45.247 0L9.375 24.457c-12.5 12.504-12.5 32.77 0 45.25l113.152 113.152L9.398 295.99c-12.503 12.503-12.503 32.769 0 45.25L24.48 356.32c12.5 12.5 32.766 12.5 45.247 0l113.132-113.132L295.99 356.32c12.503 12.5 32.769 12.5 45.25 0l15.081-15.082c12.5-12.504 12.5-32.77 0-45.25zm0 0"></path>
                                                    </g>
                                                </svg>
                                                <svg class="checkmark" xml:space="preserve" style="enable-background:new 0 0 512 512" viewBox="0 0 24 24" y="0" x="0" height="10" width="10" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" xmlns="http://www.w3.org/2000/svg">
                                                    <g>
                                                        <path class="" data-original="#000000" fill="currentColor" d="M9.707 19.121a.997.997 0 0 1-1.414 0l-5.646-5.647a1.5 1.5 0 0 1 0-2.121l.707-.707a1.5 1.5 0 0 1 2.121 0L9 14.171l9.525-9.525a1.5 1.5 0 0 1 2.121 0l.707.707a1.5 1.5 0 0 1 0 2.121z"></path>
                                                    </g>
                                                </svg>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fa fa-dot-circle-o"></i> Submit
                                </button>
                                <button type="reset" class="btn btn-danger btn-sm">
                                    <i class="fa fa-ban"></i> Reset
                                </button>
                            </div>
                        </form>
                    </div>
                    
                </div>
                
            </div>
            
        </div>
    </div>
</div>
@endsection

@section('style-libs')
    <style>
        .switch {
            /* switch */
            --switch-width: 46px;
            --switch-height: 24px;
            --switch-bg: rgb(131, 131, 131);
            --switch-checked-bg: rgb(0, 218, 80);
            --switch-offset: calc((var(--switch-height) - var(--circle-diameter)) / 2);
            --switch-transition: all .2s cubic-bezier(0.27, 0.2, 0.25, 1.51);
            /* circle */
            --circle-diameter: 18px;
            --circle-bg: #fff;
            --circle-shadow: 1px 1px 2px rgba(146, 146, 146, 0.45);
            --circle-checked-shadow: -1px 1px 2px rgba(163, 163, 163, 0.45);
            --circle-transition: var(--switch-transition);
            /* icon */
            --icon-transition: all .2s cubic-bezier(0.27, 0.2, 0.25, 1.51);
            --icon-cross-color: var(--switch-bg);
            --icon-cross-size: 6px;
            --icon-checkmark-color: var(--switch-checked-bg);
            --icon-checkmark-size: 10px;
            /* effect line */
            --effect-width: calc(var(--circle-diameter) / 2);
            --effect-height: calc(var(--effect-width) / 2 - 1px);
            --effect-bg: var(--circle-bg);
            --effect-border-radius: 1px;
            --effect-transition: all .2s ease-in-out;
        }

        .switch input {
            display: none;
        }

        .switch {
            display: inline-block;
        }

        .switch svg {
            -webkit-transition: var(--icon-transition);
            -o-transition: var(--icon-transition);
            transition: var(--icon-transition);
            position: absolute;
            height: auto;
        }

        .switch .checkmark {
            width: var(--icon-checkmark-size);
            color: var(--icon-checkmark-color);
            -webkit-transform: scale(0);
            -ms-transform: scale(0);
            transform: scale(0);
        }

        .switch .cross {
            width: var(--icon-cross-size);
            color: var(--icon-cross-color);
        }

        .slider {
            -webkit-box-sizing: border-box;
            box-sizing: border-box;
            width: var(--switch-width);
            height: var(--switch-height);
            background: var(--switch-bg);
            border-radius: 999px;
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-align: center;
            -ms-flex-align: center;
            align-items: center;
            position: relative;
            -webkit-transition: var(--switch-transition);
            -o-transition: var(--switch-transition);
            transition: var(--switch-transition);
            cursor: pointer;
        }

        .circle {
            width: var(--circle-diameter);
            height: var(--circle-diameter);
            background: var(--circle-bg);
            border-radius: inherit;
            -webkit-box-shadow: var(--circle-shadow);
            box-shadow: var(--circle-shadow);
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-align: center;
            -ms-flex-align: center;
            align-items: center;
            -webkit-box-pack: center;
            -ms-flex-pack: center;
            justify-content: center;
            -webkit-transition: var(--circle-transition);
            -o-transition: var(--circle-transition);
            transition: var(--circle-transition);
            z-index: 1;
            position: absolute;
            left: var(--switch-offset);
        }

        .slider::before {
            content: "";
            position: absolute;
            width: var(--effect-width);
            height: var(--effect-height);
            left: calc(var(--switch-offset) + (var(--effect-width) / 2));
            background: var(--effect-bg);
            border-radius: var(--effect-border-radius);
            -webkit-transition: var(--effect-transition);
            -o-transition: var(--effect-transition);
            transition: var(--effect-transition);
        }

        /* actions */

        .switch input:checked+.slider {
            background: var(--switch-checked-bg);
        }

        .switch input:checked+.slider .checkmark {
            -webkit-transform: scale(1);
            -ms-transform: scale(1);
            transform: scale(1);
        }

        .switch input:checked+.slider .cross {
            -webkit-transform: scale(0);
            -ms-transform: scale(0);
            transform: scale(0);
        }

        .switch input:checked+.slider::before {
            left: calc(100% - var(--effect-width) - (var(--effect-width) / 2) - var(--switch-offset));
        }

        .switch input:checked+.slider .circle {
            left: calc(100% - var(--circle-diameter) - var(--switch-offset));
            -webkit-box-shadow: var(--circle-checked-shadow);
            box-shadow: var(--circle-checked-shadow);
        }
    </style>
@endsection

@section('script-libs')

@endsection
@section('script')

@endsection