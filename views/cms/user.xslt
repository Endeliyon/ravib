<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:include href="../banshee/main.xslt" />
<xsl:include href="../banshee/pagination.xslt" />

<!--
//
//  Overview template
//
//-->
<xsl:template match="overview">
<table class="table table-striped table-hover table-condensed">
<thead>
<tr>
<th class="username"><a href="?order=username">Username</a></th>
<th class="id"><a href="?order=id">ID</a></th>
<th class="name"><a href="?order=fullname">Name</a></th>
<th class="email"><a href="?order=email">E-mail address</a></th>
<th class="status"><a href="?order=status">Account status</a></th>
</tr>
</thead>
<tbody>
<xsl:for-each select="users/user">
<tr class="disabled">
<xsl:if test="/output/user/@admin='yes' or @admin='no'">
<xsl:attribute name="class">click</xsl:attribute>
<xsl:attribute name="onClick">javascript:document.location='/<xsl:value-of select="/output/page" />/<xsl:value-of select="@id" />'</xsl:attribute>
</xsl:if>
<td><xsl:value-of select="username" /></td>
<td><xsl:value-of select="@id" /></td>
<td><xsl:value-of select="fullname" /></td>
<td><xsl:value-of select="email" /></td>
<td><xsl:value-of select="status" /></td>
</tr>
</xsl:for-each>
</tbody>
</table>

<div class="right">
<xsl:apply-templates select="pagination" />
</div>
<div class="btn-group left">
<a href="/{/output/page}/new" class="btn btn-default">New user</a>
<a href="/cms" class="btn btn-default">Back</a>
</div>
<div class="clear"></div>
</xsl:template>

<!--
//
//  Edit template
//
//-->
<xsl:template match="edit">
<xsl:call-template name="show_messages" />
<form action="/{/output/page}" method="post" onSubmit="javascript:hash_passwords(); return true;">
<xsl:if test="user/@id">
<input type="hidden" name="id" value="{user/@id}" />
</xsl:if>
<label for="username">Username:</label>
<input type="text" id="username" name="username" value="{user/username}" class="form-control" />
<label for="password">Password:</label>
<span class="generate"><input type="checkbox" name="generate" id="generate" onClick="javascript:generate_checkbox()">
<xsl:if test="user/generate='on'"><xsl:attribute name="checked">checked</xsl:attribute></xsl:if>
</input>Auto-generate password and send it to the user via e-mail.</span>
<input type="password" id="password" name="password" class="form-control">
<xsl:if test="user/generate='on'"><xsl:attribute name="disabled">disabled</xsl:attribute></xsl:if>
</input>
<label for="email">E-mail address:</label>
<input type="text" id="email" name="email" value="{user/email}" class="form-control" />
<label for="status">Account status:</label>
<select id="status" name="status" class="form-control">
<xsl:if test="user/@id=/output/user/@id">
<xsl:attribute name="disabled">disabled</xsl:attribute>
</xsl:if>
<xsl:for-each select="status/status">
<option value="{@id}">
<xsl:if test="@id=../../user/status">
<xsl:attribute name="selected">selected</xsl:attribute>
</xsl:if>
<xsl:value-of select="." />
</option>
</xsl:for-each>
</select>
<label for="fullname">Full name:</label>
<input type="text" id="fullname" name="fullname" value="{user/fullname}" class="form-control" />
<xsl:if test="organisations">
<label for="organisation">Organisation:</label>
<select id="organisation" name="organisation_id" class="form-control">
<xsl:for-each select="organisations/organisation">
<option value="{@id}">
<xsl:if test="@id=../../user/organisation_id">
<xsl:attribute name="selected">selected</xsl:attribute>
</xsl:if>
<xsl:value-of select="." />
</option>
</xsl:for-each>
</select>
</xsl:if>
<label for="roles">Roles:</label>
<xsl:for-each select="roles/role">
<div><input type="checkbox" name="roles[{@id}]" value="{@id}" class="role">
<xsl:if test="@enabled='no'">
<xsl:attribute name="disabled">disabled</xsl:attribute>
</xsl:if>
<xsl:if test="@checked='yes'">
<xsl:attribute name="checked">checked</xsl:attribute>
</xsl:if>
</input><xsl:value-of select="." />
<xsl:if test="@enabled='no'">
<input type="hidden" name="roles[{@id}]" value="{@id}" />
</xsl:if>
</div>
</xsl:for-each>

<div class="btn-group">
<input type="submit" name="submit_button" value="Save user" class="btn btn-default" />
<a href="/{/output/page}" class="btn btn-default">Cancel</a>
<xsl:if test="user/@id and not(user/@id=/output/user/@id)">
<input type="submit" name="submit_button" value="Delete user" class="btn btn-default" onClick="javascript:return confirm('DELETE: Are you sure?')" />
</xsl:if>
</div>

<input type="hidden" id="password_hashed" name="password_hashed" value="no" />
</form>
</xsl:template>

<!--
//
//  Result template
//
//-->
<xsl:template match="result">
<p><xsl:value-of select="." /></p>
<xsl:call-template name="redirect" />
</xsl:template>

<!--
//
//  Content template
//
//-->
<xsl:template match="content">
<img src="/images/icons/users.png" class="title_icon" />
<h1>User administration</h1>
<xsl:apply-templates select="overview" />
<xsl:apply-templates select="edit" />
<xsl:apply-templates select="result" />
</xsl:template>

</xsl:stylesheet>
