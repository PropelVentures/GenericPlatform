
                <div class="container">
                    <div class="row" >
                        <div class="col-6 col-sm-6 col-lg-3">
                            <form action="form-actions.php" method="post">
                                <div class='left-content'> <span> <img id="user_thumb" src="users_uploads/defaultImageIcon.png" border="0" width="200" height="200" class="img-thumbnail img-responsive" style="width:100%;" /> </span>
                                    <div>
                                        <input type="hidden" role="uploadcare-uploader" name="image" id="file2" data-locale="en" data-tabs="file url facebook gdrive instagram" data-images-only="false" data-path-value="false" data-preview-step="false" data-multiple="false"  value="" data-crop="650x430 minimum"/>
                                        <br />
                                        <input type="hidden" name="uploadcare_image_url" id="uploadcare_image_url" value="" />
                                        <input type="hidden" name="uploadcare_image_name" id="uploadcare_image_name" value="" />
                                        <input type="hidden" name="profile_id" id="profile_id" value="16" />
                                        <div style="margin:5% 0%">
                                            <input type="submit" class="submit btn btn-primary pull-left" name="profile_image_submit" id="login" value="SAVE">
                                            <input type="button" onclick="location.href = 'http://genericveryold.cjcornell.com/profile.php'" class="submit btn btn-primary  pull-right" name="login" value="CANCEL"/>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class='col-6 col-sm-6 col-lg-9 right-content user-profile'>
                            <ul class="nav nav-tabs" role="tablist" >
                                <li class="active"><a href="profile.php?tab=account">My Account</a></li>
                                <li ><a href="profile.php?tab=transactions">My Transactions</a></li>
                                <li ><a href="profile.php?tab=otherTransactions">Other Transactions</a></li>

                                <li ><a href="profile.php?tab=myFavorites">My Favorites</a></li>
                                <li ><a href="profile.php?tab=follow">My Follows</a></li>
                                <li ><a href="profile.php?tab=friends">My Friends</a></li>
                                <li ><a href="profile.php?tab=likes">My Likes</a></li>

                            </ul>
                            <form action='profile.php' method='post' id='user_profile_form' enctype='multipart/form-data'>
                                <div class ="setborder">
                                    <div class='form_element'>
                                        <label>
                                            Name			</label>
                                        :<br />
                                        <span>
                                            <input type='text' id='uname' class='form-control' name='uname' value='TestUser' required />
                                        </span></div>
                                    <div class='form_element'>
                                        <label>
                                            Email			</label>
                                        : <br />
                                        <span>
                                            <input type='email' id='email' class='form-control' name='email' value='testuser@gmail.com' required />
                                        </span>
                                    </div>
                                    <div style="clear:both"></div>
                                </div>
                                <div class ="belowborder">
                                    <div class='form_element'>
                                        <label>
                                            About me			</label>
                                        :<br />
                                        <span>
                                            <textarea id='aboutme' name="aboutme" class='form-control'><p>testt another test</p>
                                            </textarea>
                                        </span>
                                    </div>
                                    <div class='form_element'>
                                        <label>
                                            Interests			</label>
                                        : <br />
                                        <span>
                                            <textarea id='interests' name="interests" class='form-control'>testt 45</textarea>
                                        </span>
                                    </div>
                                    <div class='form_element'>
                                        <label>
                                            Skills			</label>
                                        : <br />
                                        <span>
                                            <textarea id='skills' name="skills" class='form-control'>test vv 67</textarea>
                                        </span>
                                    </div>
                                    <div style="clear:both"></div>
                                </div>
                                <div class="belowborder" >
                                    <div class='form_element'>
                                        <label>
                                            Company			</label>
                                        :<br />
                                        <span>
                                            <input type='text' id='company' class='form-control' name='company' value='Generic' required />
                                        </span></div>
                                    <div class='form_element'>
                                        <label>
                                            City			</label>
                                        :<br />
                                        <span>
                                            <input type='text' id='city' name='city' class='form-control' value='Hyderabad' required/>
                                        </span></div>
                                    <div style="clear:both"></div>
                                </div>
                                <div class="belowborder" >
                                   
                                    <div class='form_element'>
                                        <label>
                                            Country			</label>
                                        :<br />
                                        <span>
                                            <input type='text' id='country' class='form-control' name='country' value='INDIA' required/>
                                        </span></div>
                                    <div style="clear:both"></div>
                                </div>
                                <div class="belowborder" >
                                    <div class='form_element'>
                                        <label>
                                            Zip			</label>
                                        :<br />
                                        <span>
                                            <input type='text' id='zip' class='form-control' name='zip' value='500049[hjhjh' />
                                        </span></div>
                                    <div style="clear:both"></div>
                                </div>
                                <div class="belowborder" >
                                    <div class='form_element' style="width:98% !important">
                                        <label>
                                            Profile			</label>
                                        :<br />
                                        <span>
                                            <textarea id"description"   name="description">
                                        </textarea>
                                    </span></div>
                                <div style="clear:both"></div>
                            </div>
                            <div style="clear:both"></div>
                            <div class="belowborder" >
                                <div class='form_element'>
                                    <label>
                                        Your Twitter account			</label>
                                    :<br />
                                    <span>
                                        <input type='text' id='country' class='form-control' name='twitterAccnount' value='teast rt' />
                                    </span> </div>
                                <div class='form_element'>
                                    <label>
                                        Your Google+ account			</label>
                                    :<br />
                                    <span>
                                        <input type='text' id='country' class='form-control' name='gplusAccount' value='test' />
                                    </span></div>
                                <div class='form_element'>
                                    <label>
                                        Your Google+ account			</label>
                                    :<br />
                                    <span>
                                        <input type='text' id='country' class='form-control' name='fbAccount' value='test vv bhh' />
                                    </span></div>			
                                <input type='hidden' name='uid' value="16" />
                                <div style="clear:both"></div>
                            </div>

                            <div class="belowborder" >
                                <div class='form_element'>
                                    <label>
                                        Cross REF Tables
                                    </label>
                                    :<br />
                                    <span>
                                        <select name = 'ctable'> <option value=''>--Select Table--</option><option value=''></option><option value=''></option></select>			</span> </div>
                                <div style="clear:both"></div>
                            </div>
                            <div class='form_element update-btn2'>
                                <input type='submit' name='profile_update' value='Update Profile' class="btn btn-primary update-btn" />
                            </div>
                            <div class='form_element'>
                                <label>
                                    <a href="http://genericveryold.cjcornell.com/profile.php" ><input type='button' name='profile_cancel' value='Cancel' class="btn btn-primary update-btn" /></a>
                                </label>
                            </div>
                            <div style="clear:both"></div>
                        </form>
                        <script src="http://genericveryold.cjcornell.com//ckeditor/ckeditor.js"></script>
                        <script>
                                                //CKEDITOR.replace('description'); 
                                                CKEDITOR.replace('description', {
                                                    toolbarGroups: [
                                                        {name: 'document', groups: ['mode']}, // Line break - next group will be placed in new line.
                                                        {name: 'basicstyles', groups: ['basicstyles', 'cleanup']},
                                                        {name: 'styles', groups: ['Styles', 'Format', 'Font', 'FontSize', 'TextColor', 'BGColor']},
                                                        {name: 'insert', groups: ['Image', 'Flash', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe', 'uploadcare', 'youtube']}
                                                    ]},
                                                {
                                                    allowedContent: true
                                                });
                        </script>            <div style="clear:both"></div>
                    </div>
                </div>
            </div>
   
