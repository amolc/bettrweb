<?php
include('cfg/cfg.php');
$method=filter_text($_GET['method']);
if($method=="adduser")
{
	$usermail="";
	$userpass="";
	if(isset($_GET['email']))
	{
	$usermail=filter_text($_GET['email']);
	}
	if(isset($_GET['password']))
	{
	$userpass=filter_text($_GET['password']);
	}
	if(!$usermail)
	{
		$data['code']="999";
		$data['response']="Error, Email is required.";
		$json=json_encode($data);
		echo $json;
		die();
	}
	if(!$userpass)
	{
		$data['code']="999";
		$data['response']="Error, Password is required.";
		$json=json_encode($data);
		echo $json;
		die();
	}
	else
	{
		$valid_pass=valid_pass($userpass);
		if($valid_pass != "ok")
		{
		$data['code']="999";
		$data['response']=$valid_pass;
		$json=json_encode($data);
		echo $json;
		die();
			
		}
		else
		{
			$userpass=encrypt_str($userpass);
		}
	}
	
		$checkquery="select * from btr_users where usermail='$usermail'";
		$checkSql=@db_query($checkquery);
		if($checkSql['count']>0)
		{
				$data['code']="999";
				$data['response']="User already exists.";
				$json=json_encode($data);
				echo $json;
				die();
		}
		else
		{
			$insert_query="insert into btr_users(usermail,userpass,joinedon)values('$usermail','$userpass',".gmmktime().")";
			$insert_sql=@db_query($insert_query,3);
			if($insert_sql)
			{
				$authkey=encrypt_str($insert_sql);
				@db_query("update btr_users set authkey='$authkey' where userId=$insert_sql");
				@db_query("delete from btr_userprofile userId=$insert_sql");
				@db_query("insert into btr_userprofile(userId)values($insert_sql)");				
				$data['code']="0";
				$data['response']="User added successfully.";
				$data['authkey']="$authkey";
				$json=json_encode($data);
				echo $json;
				die();
			}
			else{
				
			}
		}
	


}
if($method=="forgotpassword")
{
	$usermail="";
	
	if(isset($_GET['email']))
	{
	$usermail=filter_text($_GET['email']);
	}
	
	if(!$usermail)
	{
		$data['code']="999";
		$data['response']="Error, Email is required.";
		$json=json_encode($data);
		echo $json;
		die();
	}
	
	
		$checkquery="select * from btr_users where usermail='$usermail'";
		$checkSql=@db_query($checkquery);
		if($checkSql['count']>0)
		{
			$mailmatter='<p>Hello User</p>
			<p>We have recieved your request to reset your account password.</p>
			<p>Please click on following link to change your '.$sitename.' password.</p>
			<p>
				<a href="'.$serverpath.'resetpassword/'.encrypt_str($checkSql['rows']['0']['userId']).'" target="_blank">Click Here To Change your password.</a></p><p>Regards</p><p>'.$sitename.'</p>';
				$mailsubject="Reset your $sitename password.";
				$mailto=$usermail;
				$from="donotreply@bettr.com";
				$headers  = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
				$headers .= "From: $sitename <$from>" . "\r\n";			
				$mail=mail($mailto,$mailsubject,$mailmatter,$headers);
				if($mail)
				{
				$data['code']="0";
				$data['response']="An email is sent to your registered mail address.";
				$json=json_encode($data);
				echo $json;
				die();
				}
				else
				{
					$data['code']="999";
					$data['response']="Error, In reseting your password. Please try again later.";
								$json=json_encode($data);
								echo $json;
								die();
				}
		}
		else
		{
			
				$data['code']="999";
				$data['response']="User not found.";
				$json=json_encode($data);
				echo $json;
				die();
		}
	


}
if($method=="updateprofile")
{
	//prfId,userId,fname,lname,gender,address1,address2,city,zipcode,country,aboutus,skills
	$authkey="";
	$fname="";
	$lname="";
	$gender="";
	$address1="";
	$address2="";
	$city="";
	$zipcode="";
	$country="";
	$aboutus="";
	$skills="";
	if(isset($_GET['authkey']))
	{
		$authkey=filter_text($_GET['authkey']);
	}
	if(isset($_GET['fname']))
	{
		$fname=filter_text($_GET['fname']);
	}
	if(isset($_GET['lname']))
	{
		$lname=filter_text($_GET['lname']);
	}
	if(isset($_GET['gender']))
	{
		$gender=filter_text($_GET['gender']);
	}
	if(isset($_GET['address1']))
	{
		$address1=filter_text($_GET['address1']);
	}
	if(isset($_GET['address2']))
	{
		$address2=filter_text($_GET['address2']);
	}
	if(isset($_GET['city']))
	{
		$city=filter_text($_GET['city']);
	}
	if(isset($_GET['zipcode']))
	{
		$zipcode=filter_text($_GET['zipcode']);
	}
	if(isset($_GET['country']))
	{
		$country=filter_text($_GET['country']);
	}
	if(isset($_GET['aboutus']))
	{
		$aboutus=filter_text($_GET['aboutus']);
	}
	if(isset($_GET['skills']))
	{
		$skills=filter_text($_GET['skills']);
	}
	if(!$authkey)
	{
		$data['code']="999";
		$data['response']="Error, Authorization Key Is Required.";
		$json=json_encode($data);
		echo $json;
		die();
	}
	else
	{
		$checkQuery="select * from btr_users where authkey='$authkey'";
		$checkSql=@db_query($checkQuery);
		if($checkSql['count']>0)
		{
			$userId=$checkSql['rows']['0']['userId'];
			if($fname)
			{
				$updateQuery="update btr_userprofile set fname='$fname' where userId=$userId";
				$updateSql=@db_query($updateQuery);
			}
			if($lname)
			{
				$updateQuery="update btr_userprofile set lname='$lname' where userId=$userId";
				$updateSql=@db_query($updateQuery);
			}
			if($gender)
			{
				$updateQuery="update btr_userprofile set gender='$gender' where userId=$userId";
				$updateSql=@db_query($updateQuery);
			}
			if($address1)
			{
				$updateQuery="update btr_userprofile set address1='$address1' where userId=$userId";
				$updateSql=@db_query($updateQuery);
			}
			if($address2)
			{
				$updateQuery="update btr_userprofile set address2='$address2' where userId=$userId";
				$updateSql=@db_query($updateQuery);
			}
			if($city)
			{
				$updateQuery="update btr_userprofile set city='$city' where userId=$userId";
				$updateSql=@db_query($updateQuery);
			}
			if($zipcode)
			{
				$updateQuery="update btr_userprofile set zipcode='$zipcode' where userId=$userId";
				$updateSql=@db_query($updateQuery);
			}
			if($country)
			{
				$updateQuery="update btr_userprofile set country='$country' where userId=$userId";
				$updateSql=@db_query($updateQuery);
			}
			if($aboutus)
			{
				$updateQuery="update btr_userprofile set aboutus='$aboutus' where userId=$userId";
				$updateSql=@db_query($updateQuery);
			}
			if($skills)
			{
				$updateQuery="update btr_userprofile set skills='$skills' where userId=$userId";
				$updateSql=@db_query($updateQuery);
			}

			if(sizeof($GLOBALS['debug_sql'])<=0)
			{
				$data['code']="0";
				$data['response']="Profile Updated Successfully.";	
				$json=json_encode($data);
				echo $json;
				die();
			}
			else
			{
				print_r($GLOBALS['debug_sql']);
			}
		}
		else
		{
			$data['code']="999";
			$data['response']="Error, Invalid Authorization Key.";
			$json=json_encode($data);
			echo $json;
			die();
		}
	}
}
if($method=="addskill")
{
	//prfId,userId,fname,lname,gender,address1,address2,city,zipcode,country,aboutus,skills
	$authkey="";
	$skill="";
	if(isset($_GET['authkey']))
	{
		$authkey=filter_text($_GET['authkey']);
	}
	if(!$authkey)
	{
		$data['code']="999";
		$data['response']="Error, Authorization Key Is Required.";
		$json=json_encode($data);
		echo $json;
		die();
	}
	if(isset($_GET['skill']))
	{
		$skill=filter_text($_GET['skill']);
	}
	if(!$skill)
	{
		$data['code']="999";
		$data['response']="Error, Skill Is Required.";
		$json=json_encode($data);
		echo $json;
		die();
	}
	
		$checkQuery="select * from btr_users where authkey='$authkey'";
		$checkSql=@db_query($checkQuery);
		if($checkSql['count']>0)
		{
			$checkSql1=@db_query("select * from btr_skills where skill='$skill'");
			if($checkSql1['count']>0)
			{
						$data['code']="999";
						$data['response']="Error, Skill already exists.";
						$json=json_encode($data);
						echo $json;
						die();
			}
			else
			{
				$insertQuery="insert into btr_skills(skill,addedon)values('$skill',".gmmktime().")";
				$insertSql=@db_query($insertQuery,3);
				if($insertSql)
				{
					$data['code']="0";
						$data['response']="Error, Skill added successfully.";
						$json=json_encode($data);
						echo $json;
						die();
				}
				
			}
		}
		else
		{
			$data['code']="999";
			$data['response']="Error, Invalid Authorization Key.";
			$json=json_encode($data);
			echo $json;
			die();
		}
	
}
if($method=="getskillslist")
{
	//prfId,userId,fname,lname,gender,address1,address2,city,zipcode,country,aboutus,skills
	$authkey="";
	if(isset($_GET['authkey']))
	{
		$authkey=filter_text($_GET['authkey']);
	}
	if(!$authkey)
	{
		$data['code']="999";
		$data['response']="Error, Authorization Key Is Required.";
		$json=json_encode($data);
		echo $json;
		die();
	}
	else
	{
		$checkQuery="select * from btr_users where authkey='$authkey'";
		$checkSql=@db_query($checkQuery);
		if($checkSql['count']>0)
		{
			$skillsquery="select * from btr_skills order by skill";
			$skillssql=@db_query($skillsquery);
			if($skillssql['count']>0)
			{
				$data['code']="0";
				$data['data']=$skillssql['rows'];
				$json=json_encode($data);
				echo $json;
				die();
				
			}
			else
			{
				$data['code']="404";
		$data['response']="Error, Skills Not Found.";
		$json=json_encode($data);
		echo $json;
		die();
			}
		}
		else
		{
			$data['code']="999";
			$data['response']="Error, Invalid Authorization Key.";
			$json=json_encode($data);
			echo $json;
			die();
		}
	}
}
if($method=="addgig")
{
	//prfId,userId,fname,lname,gender,address1,address2,city,zipcode,country,aboutus,skills
	$authkey="";
	$title="";
	$description="";
	$haveattachment="";
	$skills="";
	$proposedbudget="";
	$attachment=$_FILES['attachement'];
	if(isset($_GET['authkey']))
	{
		$authkey=filter_text($_GET['authkey']);
	}
	if(!$authkey)
	{
		$data['code']="999";
		$data['response']="Error, Authorization Key Is Required.";
		$json=json_encode($data);
		echo $json;
		die();
	}
	if(isset($_GET['title']))
	{
		$title=filter_text($_GET['title']);
	}
	if(!$title)
	{
		$data['code']="999";
		$data['response']="Error, Title Is Required.";
		$json=json_encode($data);
		echo $json;
		die();
	}
	if(isset($_GET['description']))
	{
		$description=filter_text($_GET['description']);
	}
	if(!$description)
	{
		$data['code']="999";
		$data['response']="Error, Description Is Required.";
		$json=json_encode($data);
		echo $json;
		die();
	}
	if(isset($_GET['skills']))
	{
		$skills=filter_text($_GET['skills']);
	}
	if(!$skills)
	{
		$data['code']="999";
		$data['response']="Error, Skills are required.";
		$json=json_encode($data);
		echo $json;
		die();
	}
	if(isset($_GET['proposedbudget']))
	{
		$proposedbudget=filter_text($_GET['proposedbudget']);
	}
	
	
		$checkQuery="select * from btr_users where authkey='$authkey'";
		$checkSql=@db_query($checkQuery);
		if($checkSql['count']>0)
		{
			
			$userId=$checkSql['rows']['0']['userId'];
			if(!$proposedbudget)
			{
				$proposedbudget=0;
			}
			$insert_query="insert into btr_projects(userId,prjTitle,prjdesc,skills,postedon,proposedbudget)";
			$insert_query.="values($userId,'$title','$description','$skills',".gmmktime().",$proposedbudget)";
			$insert_sql=@db_query($insert_query,3);
			if($insert_sql)
			{
			$data['code']="0";
			$data['response']="Congrats, Gig successfully added.";
			$json=json_encode($data);
			echo $json;
			die();
				
			}
			else
			{
				print_r($GLOBALS['debug_sql']);
			}
			
			
		}
		else
		{
			$data['code']="999";
			$data['response']="Error, Invalid Authorization Key.";
			$json=json_encode($data);
			echo $json;
			die();
		}
	
}
if($method=="updategig")
{
	//prfId,userId,fname,lname,gender,address1,address2,city,zipcode,country,aboutus,skills
	$authkey="";
	$title="";
	$description="";
	$haveattachment="";
	$skills="";
	$proposedbudget="";
	$gigId="";
	$attachment=$_FILES['attachement'];
	if(isset($_GET['authkey']))
	{
		$authkey=filter_text($_GET['authkey']);
	}
	if(!$authkey)
	{
		$data['code']="999";
		$data['response']="Error, Authorization Key Is Required.";
		$json=json_encode($data);
		echo $json;
		die();
	}
	if(isset($_GET['title']))
	{
		$title=filter_text($_GET['title']);
	}
	if(!$title)
	{
		$data['code']="999";
		$data['response']="Error, Title Is Required.";
		$json=json_encode($data);
		echo $json;
		die();
	}
	if(isset($_GET['description']))
	{
		$description=filter_text($_GET['description']);
	}
	if(!$description)
	{
		$data['code']="999";
		$data['response']="Error, Description Is Required.";
		$json=json_encode($data);
		echo $json;
		die();
	}
	if(isset($_GET['skills']))
	{
		$skills=filter_text($_GET['skills']);
	}
	if(!$skills)
	{
		$data['code']="999";
		$data['response']="Error, Skills are required.";
		$json=json_encode($data);
		echo $json;
		die();
	}
	if(isset($_GET['proposedbudget']))
	{
		$proposedbudget=filter_text($_GET['proposedbudget']);
	}
	if(isset($_GET['gigid']))
	{
		$gigid=filter_text($_GET['gigid']);
	}
	if(!$gigid)
	{
		$data['code']="999";
		$data['response']="Error, ID for Gig is required.";
		$json=json_encode($data);
		echo $json;
		die();
	}
	
		$checkQuery="select * from btr_users where authkey='$authkey'";
		$checkSql=@db_query($checkQuery);
		if($checkSql['count']>0)
		{
			
			$userId=$checkSql['rows']['0']['userId'];
			if(!$proposedbudget)
			{
				$proposedbudget=0;
			}

			
		}
		else
		{
			$data['code']="999";
			$data['response']="Error, Invalid Authorization Key.";
			$json=json_encode($data);
			echo $json;
			die();
		}
	
}
?>