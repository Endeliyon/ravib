<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:include href="banshee/main.xslt" />

<!--
//
//  Overview template
//
//-->
<xsl:template match="overview">
<div class="progress" onClick="javascript:$('div#categories').slideToggle()">
	<xsl:if test="done>0">
    <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="{done}" aria-valuemin="0" aria-valuemax="100" style="width:{done}%" title="Voltooid"><xsl:value-of select="done" />%</div>
	</xsl:if>
	<xsl:if test="pending>0">
    <div class="progress-bar progress-bar-warning progress-bar-striped" role="progressbar" aria-valuenow="{pending}" aria-valuemin="0" aria-valuemax="100" style="width:{pending}%" title="Ingepland"><xsl:value-of select="pending" />%</div>
	</xsl:if>
	<xsl:if test="overdue>0">
    <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="{overdue}" aria-valuemin="0" aria-valuemax="100" style="width:{overdue}%" title="Voorbij deadline"><xsl:value-of select="overdue" />%</div>
	</xsl:if>
	<xsl:if test="idle>0">
    <div class="progress-bar progress-bar-idle" role="progressbar" aria-valuenow="{idle}" aria-valuemin="0" aria-valuemax="100" style="width:{idle}%" title="Niet ingepland"><xsl:value-of select="idle" />%</div>
	</xsl:if>
</div>

<div id="categories" class="categories">
<h3>Voortgang per sectie</h3>
<table>
<xsl:for-each select="categories/category">
<div><xsl:value-of select="@key" /></div>
<div class="progress">
	<xsl:if test=".>0">
    <div class="progress-bar" role="progressbar" aria-valuenow="{.}" aria-valuemin="0" aria-valuemax="100" style="width:{.}%"><xsl:value-of select="." />%</div>
	</xsl:if>
</div>
</xsl:for-each>
</table>
</div>

<div class="case"><xsl:value-of select="@name" /></div>
<form action="/{/output/page}/{measures/@case_id}" method="post">
<table class="table table-condensed table-hover iso">
<thead>
<tr>
<th><a href="?order=isonr">#</a></th>
<th><a href="?order=isonr">Maatregel uit <xsl:value-of select="measures/@iso" /></a></th>
<th><a href="?order=urgency">Urgentie</a></th>
<th><a href="?order=person">Toegewezen aan</a></th>
<th><a href="?order=deadline">Deadline</a></th>
<th><a href="?order=done">Gereed</a></th>
<th></th>
</tr>
</thead>
<tbody>
<xsl:for-each select="measures/measure">
<xsl:if test="@category">
<tr class="category">
<td colspan="7"><xsl:value-of select="@category" /></td>
</tr>
</xsl:if>
<tr class="data" onClick="javascript:edit_progress({../@case_id}, {@id})">
<td class="relevant_{relevant}"><xsl:value-of select="number" /></td>
<td class="name relevant_{relevant}"><xsl:value-of select="name" /></td>
<td class="{risk} relevant_{relevant}"><xsl:value-of select="risk" /></td>
<td><xsl:value-of select="person" /></td>
<td><xsl:if test="overdue='yes'"><xsl:attribute name="class">overdue</xsl:attribute></xsl:if><xsl:value-of select="deadline" /></td>
<td><xsl:if test="done='yes'"><img src="/images/done.png" /></xsl:if></td>
<td><xsl:if test="info!=''"><span class="glyphicon glyphicon-info-sign" onClick="javascript:show_dialog({@id});" /></xsl:if></td>
</tr>
</xsl:for-each>
</tbody>
</table>

<div class="btn-group">
<a href="/casus" class="btn btn-default">Terug naar casussen</a>
<a href="/voortgang/personen/{measures/@case_id}" class="btn btn-default">Personen</a>
<a href="/voortgang/rapport/{measures/@case_id}" class="btn btn-default">Rapportage</a>
<a href="/voortgang/export/{measures/@case_id}" class="btn btn-default">CSV export</a>
</div>
</form>

<xsl:for-each select="measures/measure">
<div class="dialogs">
<xsl:if test="info!=''">
<div id="info_{@id}" title="{name}"><span><xsl:value-of select="info" /></span></div>
</xsl:if>
</div>
</xsl:for-each>
</xsl:template>

<!--
//
//  Edit template
//
//-->
<xsl:template match="edit">
<xsl:call-template name="show_messages" />
<label>ISO maatregel:</label>
<p><xsl:value-of select="progress/measure" /></p>
<form action="/{/output/page}/{progress/case_id}" method="post">
<input type="hidden" name="iso_measure_id" value="{progress/iso_measure_id}" />
<label for="person">Toegewezen aan:</label>
<select id="person" name="actor_id" class="form-control">
<xsl:for-each select="people/person">
<option value="{@id}"><xsl:if test="@id=../../progress/actor_id"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if><xsl:value-of select="name" /></option>
</xsl:for-each>
</select>
<label for="person">Controleur:</label>
<select id="person" name="reviewer_id" class="form-control">
<xsl:for-each select="people/person">
<option value="{@id}"><xsl:if test="@id=../../progress/reviewer_id"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if><xsl:value-of select="name" /></option>
</xsl:for-each>
</select>
<label for="deadline">Deadline:</label>
<input type="text" id="deadline" name="deadline" value="{progress/deadline}" class="form-control datepicker" />
<label for="deadline">Informatie:</label>
<textarea id="info" name="info" class="form-control"><xsl:value-of select="progress/info" /></textarea>
<div>
<label for="hours_planned">Geplande uren:</label>
<input type="text" id="hours_planned" name="hours_planned" value="{progress/hours_planned}" class="form-control" />
<label for="hours_invested">Ge&#239;nvesteerde uren:</label>
<input type="text" id="hourse_invested" name="hours_invested" value="{progress/hours_invested}" class="form-control" />
<label for="done">Gereed:</label>
<input type="checkbox" id="done" name="done"><xsl:if test="progress/done='yes'"><xsl:attribute name="checked">checked</xsl:attribute></xsl:if></input>
</div>

<div class="btn-group">
<input type="submit" name="submit_button" value="Opslaan" class="btn btn-default" />
<input type="submit" name="submit_button" value="Opslaan met notificatie" class="btn btn-default" />
<a href="/{/output/page}/{progress/case_id}" class="btn btn-default">Afbreken</a>
</div>
</form>
</xsl:template>

<!--
//
//  Content template
//
//-->
<xsl:template match="content">
<xsl:apply-templates select="breadcrumbs" />
<h1>Voortgang</h1>
<div class="case"><xsl:value-of select="case" /></div>
<xsl:apply-templates select="overview" />
<xsl:apply-templates select="edit" />
<xsl:apply-templates select="result" />

<div id="help">
<p>Klik op de voortgangsbalk om een overzicht te krijgen van de afgeronde taken per categorie.</p>
</div>
</xsl:template>

</xsl:stylesheet>
