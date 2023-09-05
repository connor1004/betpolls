/* eslint-disable no-param-reassign */
/* eslint-disable no-shadow */
import React, { Component } from 'react';
import { withRouter } from 'react-router-dom';
import {
  Modal, Card, ResourceList, Stack, TextStyle, Banner, Thumbnail, Spinner, Checkbox
} from '@shopify/polaris';
import moment from 'moment-timezone';
import classnames from 'classnames';

import Api from '../../../../apis/app';
import { GAME_STATUS, IMPORT_GAME_STATUS } from '../../../../configs/enums';

class Import extends Component {
  constructor(props) {
    super(props);
    this.state = {
      status: null,
      open: true,
      imports: [],
      isLoading: false,
      isImporting: false
    };
    this.modalRef = React.createRef();
    this.handleImport = this.handleImport.bind(this);
    this.handleClose = this.handleClose.bind(this);
    this.handleToggleSelectAll = this.handleToggleSelectAll.bind(this);
  }

  async componentDidMount() {
    this.loadImportList();
  }

  get isSelectedAll() {
    const {
      imports
    } = this.state;

    let selected = true;
    for (let i = 0, ni = imports.length; i < ni; i++) {
      if (!imports[i].selected) {
        selected = false;
        break;
      }
    }
    return selected;
  }

  async loadImportList() {
    const { search } = this.props.location.state;
    await this.setState({
      isLoading: true
    });
    const { response, body } = await Api.get('admin/bets/games/import-list',
      {
        date_start: moment(search.start_at).format('YYYY-MM-DD'),
        date_end: moment(search.end_at).format('YYYY-MM-DD'),
        league_id: search.league_id
      });

    switch (response.status) {
      case 200:
      case 201: {
        await this.setState({
          imports: body.map(item => {
            if (item.sport_game_general_info.playerone) {
              item.between_players = 1;
              item.sport_game_general_info.hometeam = item.sport_game_general_info.playerone;
              item.sport_game_general_info.awayteam = item.sport_game_general_info.playertwo;
              item.sport_game_live_info.hometeam = item.sport_game_live_info.playerone;
              item.sport_game_live_info.awayteam = item.sport_game_live_info.playertwo;
              delete item.sport_game_general_info.playerone;
              delete item.sport_game_general_info.playertwo;
              delete item.sport_game_live_info.playerone;
              delete item.sport_game_live_info.playertwo;
            }
            else {
              item.between_players = 0;
            }
            return {
              data: item,
              match: this.matchItem(item),
              selected: false
            };
          })
        });
        break;
      }
      default:
        break;
    }
    await this.setState({
      isLoading: false
    });
  }

  matchItem(data) {
    const {
      list
    } = this.props.location.state;
    const found = list.find((item) => {
      const { hometeam, awayteam } = data.sport_game_general_info;
      return (item.home_team.ref_id === hometeam.local_id) && (item.away_team.ref_id === awayteam.local_id) && (item.start_at === data.sport_game_date_time);
    });
    return found || false;
  }

  async handleClose() {
    await this.setState({
      open: false
    });
    setTimeout(() => {
      this.props.history.goBack();
    }, 200);
  }

  async handleImport() {
    const {
      search
    } = this.props.location.state;
    const {
      imports
    } = this.state;
    const data = imports.filter(item => (!!item.selected)).map((item) => {
      const { data } = item;

      let status = GAME_STATUS.NOT_STARTED;
      switch (data.sport_game_status) {
        case IMPORT_GAME_STATUS.NOT_STARTED:
          status = GAME_STATUS.NOT_STARTED;
          break;
        case IMPORT_GAME_STATUS.LIVE:
          status = GAME_STATUS.STARTED;
          break;
        case IMPORT_GAME_STATUS.ENDED:
          status = GAME_STATUS.ENDED;
          break;
        case IMPORT_GAME_STATUS.CANCELLED:
        case IMPORT_GAME_STATUS.SUSPENDED:
        default:
          status = GAME_STATUS.POSTPONED;
          break;
      }
      const ret = {
        ref_id: data.sport_game_id,
        start_at: data.sport_game_date_time,
        home_team: data.sport_game_general_info.hometeam,
        away_team: data.sport_game_general_info.awayteam,
        home_team_score: (parseInt(data.sport_game_live_info.hometeam.totalscore || 0, 0) + 0),
        away_team_score: (parseInt(data.sport_game_live_info.awayteam.totalscore || 0, 0) + 0),
        game_info: data.sport_game_live_info,
        game_general_info: data.sport_game_general_info,
        status,
        between_players: data.between_players
      };
      return ret;
    });

    await this.setState({
      isImporting: true
    });
    const { response } = await Api.post('admin/bets/games/import', {
      data: JSON.stringify(data),
      league_id: search.league_id
    });
    switch (response.status) {
      case 200:
      case 201: {
        const status = {
          title: 'Data imported successfully.',
          status: 'success'
        };
        await this.setState({
          status
        });
        break;
      }
      case 422: {
        const status = {
          title: 'Data imported failed.',
          status: 'error'
        };
        await this.setState({
          status
        });
        break;
      }
      default:
        break;
    }

    await this.setState({
      isImporting: false
    });
  }

  handleToggleSelectItem(item) {
    item.selected = !item.selected;
    this.setState({
      imports: this.state.imports
    });
  }

  handleToggleSelectAll() {
    const {
      imports
    } = this.state;

    const selected = !this.isSelectedAll;
    imports.forEach((item) => {
      item.selected = selected;
    });
    this.setState({
      imports
    });
  }

  renderItem(item, index) {
    const {
      data, match, selected
    } = item;
    const {
      league
    } = this.props.location.state;
    const homeTeamFirst = data.between_players || (league && league.sport_category && league.sport_category.home_team_first === 1);
    let first_team;
    let second_team;
    if (homeTeamFirst) {
      first_team = {
        general: data.sport_game_general_info.hometeam,
        live: data.sport_game_live_info.hometeam
      };
      second_team = {
        general: data.sport_game_general_info.awayteam,
        live: data.sport_game_live_info.awayteam
      };
    } else {
      first_team = {
        general: data.sport_game_general_info.awayteam,
        live: data.sport_game_live_info.awayteam
      };
      second_team = {
        general: data.sport_game_general_info.hometeam,
        live: data.sport_game_live_info.hometeam
      };
    }

    return (
      <div
        className={classnames({
          'game-list-item-imported': !!match
        })}
        key={`${index}`}>
        <Card>
          <ResourceList.Item>
            <Stack>
              <Stack.Item>
                <div style={{ width: '40px' }}>
                  <Checkbox
                    checked={selected}
                    onChange={this.handleToggleSelectItem.bind(this, item)}
                  />
                </div>
              </Stack.Item>
              <Stack.Item>
                <TextStyle>
                  <div style={{ width: '60px' }}>{data.sport_game_id}</div>
                </TextStyle>
              </Stack.Item>
              <Stack.Item>
                <TextStyle>
                  <div style={{ width: '160px' }}>
                    {moment(moment.utc(data.sport_game_date_time).toDate()).tz("America/New_York").format('DD/MM/YYYY hh:mm a')}
                  </div>
                </TextStyle>
              </Stack.Item>
              <Stack.Item fill>
                <div style={{ width: '200px' }}>
                  <Stack>
                    <Stack.Item>
                      <Thumbnail source={first_team.general.logo} size="small" />
                    </Stack.Item>
                    <Stack.Item>
                      {first_team.general.name}
                    </Stack.Item>
                  </Stack>
                </div>
              </Stack.Item>
              <Stack.Item>
                <div style={{ width: '80px' }}>
                  {data.sport_game_status === IMPORT_GAME_STATUS.ENDED && (
                    <TextStyle variation="strong">
                      {first_team.live.totalscore}
                      :
                      {second_team.live.totalscore}
                    </TextStyle>
                  )}
                </div>
              </Stack.Item>
              <Stack.Item fill>
                <div style={{ width: '200px' }}>
                  <Stack>
                    <Stack.Item>
                      <Thumbnail source={second_team.general.logo} size="small" />
                    </Stack.Item>
                    <Stack.Item>
                      {second_team.general.name}
                    </Stack.Item>
                  </Stack>
                </div>
              </Stack.Item>
              <Stack.Item>
                <TextStyle variation="strong">
                  <div style={{ width: '100px' }}>
                    {
                      data.sport_game_status === IMPORT_GAME_STATUS.NOT_STARTED ? 'NYT' : (
                        data.sport_game_status === IMPORT_GAME_STATUS.LIVE ? 'LIVE' : (
                          data.sport_game_status === IMPORT_GAME_STATUS.ENDED ? 'FINAL' : 'SUSPENDED'
                        ))
                    }
                  </div>
                </TextStyle>
              </Stack.Item>
            </Stack>
          </ResourceList.Item>
        </Card>
      </div>

    );
  }

  renderHeader() {
    const {
      imports
    } = this.state;
    const {
      league
    } = this.props.location.state;
    const homeTeamFirst = (league && league.sport_category && league.sport_category.home_team_first === 1);
    const between_players = league && league.sport_category && league.sport_category.between_players === 1;

    return (
      <Card>
        <ResourceList.Item>
          <Stack>
            <Stack.Item>
              <div style={{ width: '40px' }}>
                <Checkbox
                  checked={this.isSelectedAll}
                  disabled={imports.length === 0}
                  onChange={this.handleToggleSelectAll}
                />
              </div>
            </Stack.Item>
            <Stack.Item>
              <TextStyle variation="strong">
                <div style={{ width: '60px' }}>Ref id</div>
              </TextStyle>
            </Stack.Item>
            <Stack.Item>
              <TextStyle variation="strong">
                <div style={{ width: '160px' }}>Date</div>
              </TextStyle>
            </Stack.Item>
            <Stack.Item fill>
              <TextStyle variation="strong">
                <div style={{ width: '200px' }}>
                  {between_players ? 'Player one' : (homeTeamFirst ? 'Home team' : 'Away team')}
                </div>
              </TextStyle>
            </Stack.Item>
            <Stack.Item>
              <div style={{ width: '80px' }} />
            </Stack.Item>
            <Stack.Item fill>
              <TextStyle variation="strong">
                <div style={{ width: '200px' }}>
                  {between_players ? 'Player two' : (homeTeamFirst ? 'Away team' : 'Home team')}
                </div>
              </TextStyle>
            </Stack.Item>
            <Stack.Item>
              <TextStyle variation="strong">
                <div style={{ width: '100px' }}>Status</div>
              </TextStyle>
            </Stack.Item>
          </Stack>
        </ResourceList.Item>
      </Card>
    );
  }

  render() {
    const {
      open, imports, status, isLoading, isImporting
    } = this.state;
    return (
      <Modal
        large
        ref={this.modalRef}
        open={open}
        onClose={this.handleClose}
        onTransitionEnd={this.handleTransitionEnd}
        title="Import"
        disabled={isImporting}
        primaryAction={{
          content: 'Import',
          onAction: this.handleImport
        }}
        secondaryActions={[
          {
            content: 'Close',
            onAction: this.handleClose
          }
        ]}
      >
        <Modal.Section>
          {status && <Banner {...status} />}
          {this.renderHeader()}
          {imports.map((item, index) => this.renderItem(item, index))}
          {
            isLoading && (
              <div>
                <Card sectioned>
                  <Spinner size="large" />
                </Card>
              </div>
            )
          }
        </Modal.Section>
      </Modal>
    );
  }
}

export default withRouter(Import);
