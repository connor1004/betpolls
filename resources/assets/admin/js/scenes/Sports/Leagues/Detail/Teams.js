import React, { Component, Fragment } from 'react';
import {
  Card, Stack, TextStyle, ResourceList, Button, Thumbnail,
  Form, FormLayout, InlineError
} from '@shopify/polaris';
import { Formik } from 'formik';
import * as Yup from 'yup';
import Select from 'react-select';
import AsyncSelect from 'react-select/lib/Async';

import Api from '../../../../apis/app';


class Teams extends Component {
  constructor(props) {
    super(props);
    this.state = {
      leagueTeams: []
      // teams: []
    };
    this.handleTeamInputChange = this.handleTeamInputChange.bind(this);
    this.loadTeamOptions = this.loadTeamOptions.bind(this);
    this.handleAdd = this.handleAdd.bind(this);
  }

  async componentDidMount() {
    this.search();
  }

  componentWillReceiveProps() {
    this.search();
  }

  async getTeamOptions(inputValue) {
    const {
      league
    } = this.props;
    const {
      body
    } = await Api.get('admin/sports/teams/search', {
      sport_category_id: league.sport_category_id,
      name: inputValue
    });
    return body;
  }

  async loadTeamOptions(inputValue, callback) {
    callback(await this.getTeamOptions(inputValue));
  }

  async search() {
    const {
      league
    } = this.props;

    const {
      body
    } = await Api.get(`admin/sports/leagues/${league.id}/teams`);
    this.setState({
      leagueTeams: body
    });
  }

  handleTeamInputChange(teamInputValue) {
    return teamInputValue;
  }

  async handleAdd(values, bags) {
    const {
      league
    } = this.props;

    const params = {
      league_id: league.id,
      team_id: values.team.id,
      league_division_id: values.league_division ? values.league_division.id : 0
    };
    await bags.setSubmitting(true);
    await Api.post(`admin/sports/leagues/${league.id}/teams`, params);
    await bags.setSubmitting(false);
    await this.search();
  }

  async handleDelete(data) {
    const {
      league
    } = this.props;
    await Api.delete(`admin/sports/leagues/${league.id}/teams/${data.team_id}`);
    await this.search();
  }

  renderHeader() {
    return (
      <Card>
        <ResourceList.Item>
          <Stack>
            <Stack.Item fill>
              <TextStyle variation="strong">
                <div style={{ width: '300px' }}>Team</div>
              </TextStyle>
            </Stack.Item>
            <Stack.Item fill>
              <TextStyle variation="strong">
                <div style={{ width: '300px' }}>Division</div>
              </TextStyle>
            </Stack.Item>
            <Stack.Item>
              <TextStyle variation="strong">
                <div style={{ width: '60px' }}>Action</div>
              </TextStyle>
            </Stack.Item>
          </Stack>
        </ResourceList.Item>
      </Card>
    );
  }

  renderItem(data, index) {
    return (
      <div key={`${index}`}>
        <Card>
          <ResourceList.Item key={`${index}`}>
            <Stack>
              <Stack.Item fill>
                <div style={{ width: '300px' }}>
                  <Stack>
                    <Stack.Item>
                      <Thumbnail source={data.team.logo} size="small" />
                    </Stack.Item>
                    <Stack.Item>
                      {data.team.name}
                    </Stack.Item>
                  </Stack>
                </div>
              </Stack.Item>
              <Stack.Item fill>
                <div style={{ width: '300px' }}>
                  {data.league_division && data.league_division.full_name}
                </div>
              </Stack.Item>
              <Stack.Item>
                <div style={{ width: '60px' }}>
                  <Button
                    destructive
                    icon="delete"
                    onClick={this.handleDelete.bind(this, data)}
                  />
                </div>
              </Stack.Item>
            </Stack>
          </ResourceList.Item>
        </Card>
      </div>
    );
  }

  renderAdd() {
    return (
      <Card sectioned>
        <Formik
          ref={this.formikRef}
          initialValues={{
            team: null,
            league_division: null
          }}
          validationSchema={
            Yup.object().shape({
              team: Yup.mixed().required('Team is required!')
            })
          }
          onSubmit={this.handleAdd}
          render={({
            values,
            errors,
            status,
            touched,
            setFieldValue,
            handleBlur,
            handleSubmit,
            isSubmitting
          }) => (
            <Form onSubmit={handleSubmit}>
              <FormLayout>
                {status && <InlineError message={status} />}
                <Stack>
                  <Stack.Item fill>
                    <AsyncSelect
                      cacheOptions
                      loadOptions={this.loadTeamOptions}
                      value={values.team}
                      getOptionLabel={(value => (value.name))}
                      getOptionValue={(value => (value.id))}
                      onInputChange={this.handleTeamInputChange}
                      onBlur={handleBlur}
                      onChange={(value) => {
                        setFieldValue('team', value);
                      }}
                    />
                    {
                      (touched.team && errors.team) && <InlineError message={errors.team} />
                    }
                  </Stack.Item>
                  <Stack.Item fill>
                    <Select
                      options={this.props.leagueDivisions}
                      value={values.league_division}
                      getOptionLabel={(value => (value.full_name))}
                      getOptionValue={(value => (value.id))}
                      onBlur={handleBlur}
                      onChange={(value) => {
                        setFieldValue('league_division', value);
                      }}
                    />
                    {
                      (touched.league_division && errors.league_division) && <InlineError message={errors.league_division} />
                    }
                  </Stack.Item>
                  <Stack.Item>
                    <Button
                      loading={isSubmitting}
                      submit
                      primary
                      icon="circlePlus"
                      >
                        Add
                    </Button>
                  </Stack.Item>
                </Stack>
              </FormLayout>
            </Form>
          )}
        />
      </Card>
    );
  }

  render() {
    const {
      leagueTeams
    } = this.state;

    return (
      <Fragment>
        {this.renderHeader()}
        {
          (leagueTeams && leagueTeams.length > 0) && (
            leagueTeams.map((data, index) => (this.renderItem(data, index)))
          )
        }
        {this.renderAdd()}
      </Fragment>
    );
  }
}

Teams.defaultProps = {
  league: {},
  teams: [],
  leagueDivisions: []
};

export default Teams;
